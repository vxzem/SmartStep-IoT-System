<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStep | Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lexend:wght@700;800&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-dark: #05162e;
            --navy-mid: #0a2447;
            --gold: #e5c158;
            --gold-soft: #fdf9ec;
            --white: #ffffff;
            --bg-slate: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-slate); color: var(--text-main); display: flex; height: 100vh; overflow: hidden; }

        /* SIDEBAR */
        .sidebar { width: 260px; background: var(--navy-dark); color: white; padding: 30px 20px; display: flex; flex-direction: column; flex-shrink: 0; }
        .logo { font-family: 'Lexend', sans-serif; color: var(--gold); font-size: 1.5rem; margin-bottom: 8px; }
        .nav-section { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.3); margin: 20px 0 8px; padding: 0 5px; }
        .nav-link { display: flex; align-items: center; gap: 10px; padding: 11px 15px; text-decoration: none; color: rgba(255,255,255,0.7); border-radius: 8px; transition: 0.2s; font-size: 0.88rem; margin-bottom: 3px; }
        .nav-link:hover { background: rgba(229,193,88,0.08); color: white; }
        .nav-link.active { background: rgba(229,193,88,0.15); color: var(--gold); font-weight: 600; }
        .nav-icon { font-size: 1rem; width: 18px; text-align: center; }
        .sidebar-footer { margin-top: auto; }
        .logout-btn { display: flex; align-items: center; gap: 8px; padding: 11px 15px; color: rgba(255,255,255,0.4); cursor: pointer; border-radius: 8px; font-size: 0.85rem; transition: 0.2s; }
        .logout-btn:hover { color: var(--danger); background: rgba(239,68,68,0.08); }
        .node-info { font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; opacity: 0.35; margin-top: 12px; padding: 0 5px; }
        .notif-badge { background: var(--gold); color: var(--navy-dark); border-radius: 10px; padding: 1px 7px; font-size: 0.65rem; margin-left: auto; font-weight: 700; }

        /* MAIN */
        .main-content { flex: 1; overflow-y: auto; padding: 35px; }

        /* TOP HEADER ROW */
        .top-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; gap: 20px; }
        .page-header-content { flex: 1; }
        .page-header-content h1 { font-family: 'Lexend', sans-serif; font-size: 1.7rem; color: var(--navy-dark); margin: 0 0 4px 0; }
        .page-header-content p { color: var(--text-muted); font-size: 0.88rem; margin: 0; }
        .right-header { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }

        /* USER BADGE */
        .user-badge { display: flex; align-items: center; gap: 12px; background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 12px 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: all 0.25s ease; }
        .user-badge:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.09); border-color: var(--gold); transform: translateY(-1px); }
        .avatar { width: 40px; height: 40px; background: var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--navy-dark); font-size: 1rem; flex-shrink: 0; box-shadow: 0 3px 8px rgba(229,193,88,0.4); }
        .user-name { font-size: 0.96rem; font-weight: 600; color: var(--navy-dark); line-height: 1.25; }
        .user-role { font-size: 0.74rem; color: var(--gold); font-family: 'JetBrains Mono', monospace; font-weight: 500; letter-spacing: 0.6px; }

        /* FILTER + MARK ALL ROW */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; }
        .filter-tabs { display: flex; gap: 4px; background: var(--white); border: 1px solid var(--border); border-radius: 10px; padding: 4px; }
        .filter-btn { padding: 7px 16px; border-radius: 7px; border: none; cursor: pointer; font-family: 'Inter', sans-serif; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); background: transparent; transition: 0.2s; }
        .filter-btn.active { background: var(--navy-dark); color: var(--white); }
        .toolbar-actions { display: flex; gap: 8px; }
        .btn-sm { padding: 8px 16px; border-radius: 8px; font-family: 'Inter', sans-serif; font-weight: 600; font-size: 0.8rem; cursor: pointer; border: none; transition: 0.2s; }
        .btn-sm:hover { transform: translateY(-1px); }
        .btn-sm-outline { background: var(--white); border: 1.5px solid var(--border); color: var(--text-muted); }
        .btn-sm-outline:hover { border-color: var(--navy-dark); color: var(--text-main); }
        .btn-sm-gold { background: var(--gold); color: var(--navy-dark); }

        /* LAYOUT: 2-col */
        .notif-layout { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }

        /* CARD */
        .card { background: var(--white); border: 1px solid var(--border); border-radius: 14px; padding: 24px; }
        .card-title { font-size: 0.72rem; font-weight: 700; color: var(--navy-dark); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 18px; border-bottom: 1px solid var(--border); padding-bottom: 12px; display: flex; align-items: center; gap: 8px; }
        .ct-icon { color: var(--gold); }
        .card-subtitle { font-size: 0.75rem; color: var(--text-muted); margin-left: auto; font-weight: 500; text-transform: none; letter-spacing: 0; font-family: 'JetBrains Mono', monospace; }

        /* ALERT ITEMS */
        .alert-list { display: flex; flex-direction: column; gap: 10px; }
        .alert-item {
            display: flex;
            gap: 14px;
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--white);
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }
        .alert-item:hover { border-color: var(--gold); box-shadow: 0 4px 12px rgba(229,193,88,0.1); }
        .alert-item.unread { background: var(--gold-soft); border-color: rgba(229,193,88,0.45); }
        .alert-item.hidden { display: none; }

        .alert-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .icon-warning { background: #fffbeb; }
        .icon-success { background: #f0fdf4; }
        .icon-info { background: #eff6ff; }
        .icon-system { background: #f1f5f9; }
        .icon-report { background: #fdf4ff; }
        .icon-peak { background: #fff7ed; }

        .alert-body { flex: 1; min-width: 0; }
        .alert-body h4 { font-size: 0.88rem; font-weight: 600; color: var(--text-main); margin-bottom: 3px; }
        .alert-body p { font-size: 0.78rem; color: var(--text-muted); line-height: 1.5; }
        .alert-meta { display: flex; align-items: center; gap: 8px; margin-top: 6px; }
        .alert-time { font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: var(--text-muted); }
        .alert-tag {
            font-size: 0.62rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .tag-warning { background: #fef3c7; color: #92400e; }
        .tag-success { background: #d1fae5; color: #065f46; }
        .tag-info { background: #dbeafe; color: #1e40af; }
        .tag-system { background: #f1f5f9; color: #475569; }
        .tag-report { background: #fae8ff; color: #7e22ce; }
        .tag-peak { background: #ffedd5; color: #9a3412; }

        .unread-dot { width: 8px; height: 8px; background: var(--gold); border-radius: 50%; flex-shrink: 0; margin-top: 6px; }

        /* DISMISS BUTTON */
        .dismiss-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.75rem;
            color: var(--text-muted);
            opacity: 0;
            transition: opacity 0.15s;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .dismiss-btn:hover { background: var(--border); color: var(--text-main); }
        .alert-item:hover .dismiss-btn { opacity: 1; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 48px 24px; }
        .empty-state .empty-icon { font-size: 2.5rem; margin-bottom: 12px; opacity: 0.4; }
        .empty-state p { font-size: 0.88rem; color: var(--text-muted); }

        /* SIDEBAR SUMMARY CARD */
        .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border); }
        .summary-row:last-child { border: none; padding-bottom: 0; }
        .summary-label { display: flex; align-items: center; gap: 10px; font-size: 0.83rem; color: var(--text-main); }
        .summary-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .summary-count { font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; font-weight: 700; color: var(--navy-dark); background: var(--bg-slate); padding: 2px 10px; border-radius: 20px; }

        /* ENABLED SOURCES CARD */
        .source-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); }
        .source-row:last-child { border: none; padding-bottom: 0; }
        .source-name { font-size: 0.82rem; color: var(--text-main); }
        .source-status { font-size: 0.7rem; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .dot-on { width: 7px; height: 7px; background: var(--success); border-radius: 50%; }
        .dot-off { width: 7px; height: 7px; background: #cbd5e1; border-radius: 50%; }
        .on-text { color: var(--success); }
        .off-text { color: var(--text-muted); }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="logo">⚡ SmartStep</div>

        <div class="nav-section">Main</div>
        <a href="user-dash.html" class="nav-link"><span class="nav-icon"></span> My Dashboard</a>
        <a href="user-analytics.html" class="nav-link"><span class="nav-icon"></span> General Analytics</a>
        <a href="user-settings.html" class="nav-link"><span class="nav-icon"></span> My Settings</a>
        <a href="user_notif.php" class="nav-link active"><span class="nav-icon"></span> Notifications <span class="notif-badge" id="sidebar-badge">3</span></a>

        <div class="sidebar-footer">
            <div class="logout-btn" onclick="window.location.href='login.html'">🚪 Sign Out</div>
            <div class="node-info">Node: ESP8266-031<br>Ver: 1.3.0-PRO · User Mode</div>
        </div>
    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- HEADER -->
        <div class="top-header-row">
            <div class="page-header-content">
                <p style="color:var(--gold);font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:6px;">
                    SmartStep Dashboard · Section 3.2
                </p>
                <h1>Notifications</h1>
                <p>Alerts from your active notification preferences</p>
            </div>
            <div class="right-header">
                <div class="user-badge">
                    <div class="avatar">DM</div>
                    <div>
                        <div class="user-name">Dominic Magalso</div>
                        <div class="user-role">USER · ACTIVE</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="filter-tabs">
                <button class="filter-btn active" onclick="filterAlerts('all', this)">All</button>
                <button class="filter-btn" onclick="filterAlerts('unread', this)">Unread</button>
                <button class="filter-btn" onclick="filterAlerts('warning', this)">⚠ Warnings</button>
                <button class="filter-btn" onclick="filterAlerts('system', this)">⚙ System</button>
                <button class="filter-btn" onclick="filterAlerts('report', this)">📋 Reports</button>
            </div>
            <div class="toolbar-actions">
                <button class="btn-sm btn-sm-outline" onclick="markAllRead()">Mark all read</button>
                <button class="btn-sm btn-sm-outline" onclick="dismissAll()">Clear all</button>
            </div>
        </div>

        <!-- 2-COLUMN LAYOUT -->
        <div class="notif-layout">

            <!-- ALERT FEED -->
            <div class="card">
                <div class="card-title">
                    <span class="ct-icon">🔔</span> Alert Feed
                    <span class="card-subtitle" id="unread-count-label">3 unread</span>
                </div>

                <div class="alert-list" id="alert-list">

                    <!-- Battery Low (enabled in settings) -->
                    <div class="alert-item unread" data-type="warning" data-id="1">
                        <div class="alert-icon-wrap icon-warning">⚠️</div>
                        <div class="alert-body">
                            <h4>Battery Low — Tile 04</h4>
                            <p>Tile 04 state of charge dropped to 76%. Efficiency down 18% from baseline. Admin has been notified.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Today · 11:42 AM</span>
                                <span class="alert-tag tag-warning">Battery Low</span>
                            </div>
                        </div>
                        <div class="unread-dot"></div>
                        <button class="dismiss-btn" onclick="dismissAlert(1)" title="Dismiss">✕</button>
                    </div>

                    <!-- Charging Port Available (enabled) -->
                    <div class="alert-item unread" data-type="info" data-id="2">
                        <div class="alert-icon-wrap icon-info">🔌</div>
                        <div class="alert-body">
                            <h4>Charging Port B Available</h4>
                            <p>USB Port B is now free and ready for device charging.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Today · 10:15 AM</span>
                                <span class="alert-tag tag-info">Charging Port</span>
                            </div>
                        </div>
                        <div class="unread-dot"></div>
                        <button class="dismiss-btn" onclick="dismissAlert(2)" title="Dismiss">✕</button>
                    </div>

                    <!-- Weekly Energy Report (enabled) -->
                    <div class="alert-item unread" data-type="report" data-id="3">
                        <div class="alert-icon-wrap icon-report">📋</div>
                        <div class="alert-body">
                            <h4>Weekly Report: +8.4% Growth</h4>
                            <p>This week's generation was 6,840 Wh — up from last week's 6,310 Wh.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 31 · 8:00 AM</span>
                                <span class="alert-tag tag-report">Weekly Report</span>
                            </div>
                        </div>
                        <div class="unread-dot"></div>
                        <button class="dismiss-btn" onclick="dismissAlert(3)" title="Dismiss">✕</button>
                    </div>

                    <!-- Mat Condition Warning (enabled) -->
                    <div class="alert-item" data-type="warning" data-id="4">
                        <div class="alert-icon-wrap icon-warning">🔲</div>
                        <div class="alert-body">
                            <h4>Mat Condition Warning — Tile 04</h4>
                            <p>Tile 04 performance has degraded. Estimated maintenance check within 48 hours.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 31 · 7:55 AM</span>
                                <span class="alert-tag tag-warning">Mat Condition</span>
                            </div>
                        </div>
                        <button class="dismiss-btn" onclick="dismissAlert(4)" title="Dismiss">✕</button>
                    </div>

                    <!-- System Restart Notice (disabled in settings — shown as muted) -->
                    <div class="alert-item" data-type="system" data-id="5">
                        <div class="alert-icon-wrap icon-system">⚙️</div>
                        <div class="alert-body">
                            <h4>System Restart Completed</h4>
                            <p>ESP8266 restarted successfully. All 4 sensors re-initialized and reporting normally.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 30 · 2:05 PM</span>
                                <span class="alert-tag tag-system">System</span>
                            </div>
                        </div>
                        <button class="dismiss-btn" onclick="dismissAlert(5)" title="Dismiss">✕</button>
                    </div>

                    <!-- Sensor Recalibration (system) -->
                    <div class="alert-item" data-type="system" data-id="6">
                        <div class="alert-icon-wrap icon-success">✅</div>
                        <div class="alert-body">
                            <h4>Sensor Recalibration Done</h4>
                            <p>All 4 tiles recalibrated to factory baseline. Peak accuracy restored.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 29 · 9:30 AM</span>
                                <span class="alert-tag tag-success">Recalibration</span>
                            </div>
                        </div>
                        <button class="dismiss-btn" onclick="dismissAlert(6)" title="Dismiss">✕</button>
                    </div>

                    <!-- Peak Hour Alert (disabled in settings — informational) -->
                    <div class="alert-item" data-type="peak" data-id="7">
                        <div class="alert-icon-wrap icon-peak">⚡</div>
                        <div class="alert-body">
                            <h4>Peak Hour Alert — High Generation</h4>
                            <p>All 4 tiles operating at peak capacity. Harvesting rate: 1.24 Wh/min.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 28 · 12:00 PM</span>
                                <span class="alert-tag tag-peak">Peak Hour</span>
                            </div>
                        </div>
                        <button class="dismiss-btn" onclick="dismissAlert(7)" title="Dismiss">✕</button>
                    </div>

                    <!-- Gateway Reconnect (system) -->
                    <div class="alert-item" data-type="system" data-id="8">
                        <div class="alert-icon-wrap icon-system">🌐</div>
                        <div class="alert-body">
                            <h4>Gateway Reconnected</h4>
                            <p>ESP8266-031 re-established connection to the network gateway after brief interruption.</p>
                            <div class="alert-meta">
                                <span class="alert-time">Mar 28 · 7:10 AM</span>
                                <span class="alert-tag tag-system">System</span>
                            </div>
                        </div>
                        <button class="dismiss-btn" onclick="dismissAlert(8)" title="Dismiss">✕</button>
                    </div>

                </div>

                <!-- Empty state (hidden by default) -->
                <div class="empty-state" id="empty-state" style="display:none;">
                    <div class="empty-icon">🔔</div>
                    <p>No notifications to show.</p>
                </div>
            </div>

            <!-- RIGHT SIDEBAR CARDS -->
            <div style="display:flex;flex-direction:column;gap:16px;">

                <!-- Summary by type -->
                <div class="card">
                    <div class="card-title"><span class="ct-icon">📊</span> Summary</div>
                    <div class="summary-row">
                        <div class="summary-label"><span class="summary-dot" style="background:#f59e0b;"></span> Warnings</div>
                        <span class="summary-count" id="count-warning">2</span>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label"><span class="summary-dot" style="background:#3b82f6;"></span> Info</div>
                        <span class="summary-count" id="count-info">1</span>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label"><span class="summary-dot" style="background:#a855f7;"></span> Reports</div>
                        <span class="summary-count" id="count-report">1</span>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label"><span class="summary-dot" style="background:#64748b;"></span> System</div>
                        <span class="summary-count" id="count-system">3</span>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label"><span class="summary-dot" style="background:#f97316;"></span> Peak Hour</div>
                        <span class="summary-count" id="count-peak">1</span>
                    </div>
                </div>

                <!-- Enabled notification sources (mirrors settings toggles) -->
                <div class="card">
                    <div class="card-title"><span class="ct-icon">⚙️</span> Active Alert Sources</div>
                    <div class="source-row">
                        <span class="source-name">Battery Low Alert</span>
                        <span class="source-status"><span class="dot-on"></span><span class="on-text">On</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Charging Port Available</span>
                        <span class="source-status"><span class="dot-on"></span><span class="on-text">On</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Mat Condition Warning</span>
                        <span class="source-status"><span class="dot-on"></span><span class="on-text">On</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">System Restart Notice</span>
                        <span class="source-status"><span class="dot-off"></span><span class="off-text">Off</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Weekly Energy Report</span>
                        <span class="source-status"><span class="dot-on"></span><span class="on-text">On</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Peak Hour Alerts</span>
                        <span class="source-status"><span class="dot-off"></span><span class="off-text">Off</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Push Notifications</span>
                        <span class="source-status"><span class="dot-on"></span><span class="on-text">On</span></span>
                    </div>
                    <div class="source-row">
                        <span class="source-name">Email & SMS</span>
                        <span class="source-status"><span class="dot-off"></span><span class="off-text">Off</span></span>
                    </div>
                    <div style="margin-top:16px;">
                        <a href="user-settings.html" style="display:block;text-align:center;font-size:0.8rem;font-weight:600;color:var(--navy-dark);text-decoration:none;padding:9px;border:1.5px solid var(--border);border-radius:8px;transition:0.2s;" onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border)'">
                            ⚙️ Manage in Settings
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
        let dismissed = new Set();
        let currentFilter = 'all';

        function updateUnreadCount() {
            const unread = document.querySelectorAll('.alert-item.unread:not(.hidden)').length;
            const label = document.getElementById('unread-count-label');
            const badge = document.getElementById('sidebar-badge');
            label.textContent = unread > 0 ? `${unread} unread` : 'all read';
            badge.textContent = unread;
            badge.style.display = unread > 0 ? '' : 'none';
        }

        function checkEmpty() {
            const visible = document.querySelectorAll('.alert-item:not(.hidden)').length;
            document.getElementById('empty-state').style.display = visible === 0 ? 'block' : 'none';
        }

        function dismissAlert(id) {
            const el = document.querySelector(`.alert-item[data-id="${id}"]`);
            if (el) {
                el.style.opacity = '0';
                el.style.transform = 'translateX(20px)';
                el.style.transition = 'all 0.25s ease';
                setTimeout(() => {
                    el.classList.add('hidden');
                    updateUnreadCount();
                    checkEmpty();
                }, 250);
            }
        }

        function markAllRead() {
            document.querySelectorAll('.alert-item.unread').forEach(el => {
                el.classList.remove('unread');
                const dot = el.querySelector('.unread-dot');
                if (dot) dot.remove();
            });
            updateUnreadCount();
        }

        function dismissAll() {
            document.querySelectorAll('.alert-item:not(.hidden)').forEach(el => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.2s ease';
            });
            setTimeout(() => {
                document.querySelectorAll('.alert-item').forEach(el => el.classList.add('hidden'));
                updateUnreadCount();
                checkEmpty();
            }, 220);
        }

        function filterAlerts(type, btn) {
            currentFilter = type;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.alert-item').forEach(el => {
                const elType = el.dataset.type;
                const show = type === 'all'
                    || (type === 'unread' && el.classList.contains('unread'))
                    || type === elType;
                el.classList.toggle('hidden', !show);
            });
            checkEmpty();
        }

        // Mark single item as read on click
        document.getElementById('alert-list').addEventListener('click', function(e) {
            const item = e.target.closest('.alert-item');
            if (!item || e.target.closest('.dismiss-btn')) return;
            if (item.classList.contains('unread')) {
                item.classList.remove('unread');
                const dot = item.querySelector('.unread-dot');
                if (dot) dot.remove();
                updateUnreadCount();
            }
        });

        updateUnreadCount();
    </script>
</body>
</html>
