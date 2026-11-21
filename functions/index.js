const { onValueWritten } = require("firebase-functions/v2/database");
const { createClient } = require("@supabase/supabase-js");

// Your specific Project Credentials
const SUPABASE_URL = process.env.SUPABASE_URL || "https://qvadzitiwgkhzlgjmdac.supabase.co";
const SUPABASE_SERVICE_ROLE_KEY = process.env.SUPABASE_SERVICE_ROLE_KEY || ""; 

const supabase = createClient(SUPABASE_URL, SUPABASE_SERVICE_ROLE_KEY);

exports.smartStepBridge = onValueWritten("/mats/{uid}/live", async (event) => {
    const data = event.data.after.val();
    const uid = event.params.uid;

    if (!data || data.voltage < 0.2) return;

    // Pipe the data to Supabase
    const { error } = await supabase
        .from('energy_logs')
        .insert([{ 
            user_id: uid, 
            voltage: data.voltage, 
            tile_id: data.tile_id || 1,
            created_at: new Date().toISOString()
        }]);

    if (error) console.error("Supabase Error:", error.message);
});