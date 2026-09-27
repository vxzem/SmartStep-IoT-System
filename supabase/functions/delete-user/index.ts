// Supabase Edge Function: delete-user
//
// WHY THIS NEEDS TO BE A SERVER-SIDE FUNCTION:
// Deleting a user's login entirely (auth.users) can only be done with the
// Supabase Admin API (auth.admin.deleteUser), which requires the service_role
// key. That key must NEVER be shipped to the browser, so the actual deletion
// happens here rather than in security.html.
//
// DEPLOY:
//   supabase functions deploy delete-user
// (Uses the same SUPABASE_SERVICE_ROLE_KEY secret set up for
// approve-email-change — no need to set it again if that's already done.)
//
// This checks the caller is an authenticated admin (profiles.role = 'admin')
// before doing anything, then:
//   1. Deletes the profiles row (in case it isn't already cascade-deleted)
//   2. Deletes the auth.users record via the Admin API

import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
};

function json(body: unknown, status: number) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, "Content-Type": "application/json" },
  });
}

Deno.serve(async (req) => {
  // Browsers send a CORS preflight OPTIONS request before the real POST —
  // without this, the actual request never leaves the browser and the
  // fetch() call fails with an opaque network error, not a JSON error body.
  if (req.method === "OPTIONS") {
    return new Response(null, { headers: corsHeaders });
  }

  try {
    if (req.method !== "POST") {
      return json({ error: "Method not allowed" }, 405);
    }

    const authHeader = req.headers.get("Authorization") || "";
    const callerJwt = authHeader.replace("Bearer ", "");
    if (!callerJwt) {
      return json({ error: "Missing Authorization header" }, 401);
    }

    const { user_id } = await req.json();
    if (!user_id) {
      return json({ error: "user_id is required" }, 400);
    }

    const SUPABASE_URL = Deno.env.get("SUPABASE_URL")!;
    const ANON_KEY = Deno.env.get("SUPABASE_ANON_KEY")!;
    const SERVICE_ROLE_KEY = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;

    // Client scoped to the caller's own JWT — used only to verify who's calling.
    const callerClient = createClient(SUPABASE_URL, ANON_KEY, {
      global: { headers: { Authorization: `Bearer ${callerJwt}` } },
    });

    const { data: { user: caller }, error: callerErr } = await callerClient.auth.getUser();
    if (callerErr || !caller) {
      return json({ error: "Invalid session" }, 401);
    }

    // Prevent an admin from deleting their own account through this tool
    if (caller.id === user_id) {
      return json({ error: "You cannot remove your own account here." }, 400);
    }

    const { data: callerProfile, error: profileErr } = await callerClient
      .from("profiles")
      .select("role")
      .eq("id", caller.id)
      .single();

    if (profileErr || callerProfile?.role !== "admin") {
      return json({ error: "Admin privileges required" }, 403);
    }

    // Admin client — only ever used server-side, never sent to the browser.
    const adminClient = createClient(SUPABASE_URL, SERVICE_ROLE_KEY);

    // Delete the profile row first (harmless if a DB cascade already removes
    // it when the auth user is deleted below — this just guarantees cleanup
    // either way and avoids leaving an orphaned profile if that FK isn't set).
    const { error: profileDeleteErr } = await adminClient
      .from("profiles")
      .delete()
      .eq("id", user_id);
    if (profileDeleteErr) {
      return json({ error: `Failed to delete profile: ${profileDeleteErr.message}` }, 400);
    }

    const { error: authDeleteErr } = await adminClient.auth.admin.deleteUser(user_id);
    if (authDeleteErr) {
      // Profile is already gone at this point — surface this clearly since
      // it means the auth account is now orphaned and needs manual cleanup.
      return json({ error: `Profile deleted, but failed to delete auth account: ${authDeleteErr.message}` }, 207);
    }

    return json({ success: true }, 200);
  } catch (err) {
    return json({ error: err.message || "Unexpected error" }, 500);
  }
});
