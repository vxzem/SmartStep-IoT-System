// Supabase Edge Function: approve-email-change
//
// WHY THIS NEEDS TO BE A SERVER-SIDE FUNCTION:
// Changing another user's email in auth.users can only be done with the
// Supabase Admin API (auth.admin.updateUserById), which requires the
// service_role key. That key must NEVER be shipped to the browser — anyone
// who read it out of your page's JS could take over any account. So the
// actual update has to happen here, on Supabase's servers, not in security.html.
//
// DEPLOY:
//   supabase functions deploy approve-email-change
//   supabase secrets set SUPABASE_SERVICE_ROLE_KEY=your-service-role-key
// (SUPABASE_URL and SUPABASE_ANON_KEY are already available to every
// function automatically — no need to set those yourself.)
//
// This function checks that the caller is an authenticated admin (profiles.role
// = 'admin') before doing anything, then:
//   1. Applies the new email via the Admin API
//   2. Marks the request row 'approved'

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

    const { request_id, user_id, new_email } = await req.json();
    if (!request_id || !user_id || !new_email) {
      return json({ error: "request_id, user_id and new_email are required" }, 400);
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

    const { error: updateErr } = await adminClient.auth.admin.updateUserById(user_id, {
      email: new_email,
      email_confirm: true, // skip the confirmation-link step since an admin already approved this
    });
    if (updateErr) {
      return json({ error: updateErr.message }, 400);
    }

    const { error: requestErr } = await adminClient
      .from("email_change_requests")
      .update({ status: "approved", reviewed_at: new Date().toISOString(), reviewed_by: caller.id })
      .eq("id", request_id);
    if (requestErr) {
      // Email was already changed at this point — surface the mismatch clearly
      // rather than silently leaving the request stuck as 'pending'.
      return json({ error: `Email updated, but failed to mark request approved: ${requestErr.message}` }, 207);
    }

    return json({ success: true }, 200);
  } catch (err) {
    return json({ error: err.message || "Unexpected error" }, 500);
  }
});
