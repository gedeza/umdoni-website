/**
 * Automatic Session Timeout - Security Feature
 *
 * Monitors user activity and automatically logs out after inactivity period
 * Shows warning before logout with option to stay logged in
 *
 * Configuration:
 * - Timeout: 30 minutes (28 min idle + 2 min warning)
 * - Warning: 2 minutes before logout
 * - Logs: Activity log entry on auto-logout
 *
 * @author Nhlanhla Mnyandu (nhlanhla@isutech.co.za)
 * @date December 8, 2025
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        IDLE_TIMEOUT: 28 * 60 * 1000,      // 28 minutes in milliseconds
        WARNING_TIMEOUT: 2 * 60 * 1000,    // 2 minutes in milliseconds
        TOTAL_TIMEOUT: 30 * 60 * 1000,     // 30 minutes total
        CHECK_INTERVAL: 1000,              // Check every second
        PING_INTERVAL: 5 * 60 * 1000,      // Ping server every 5 minutes
        LOGOUT_URL: '/authentication/logout',
        PING_URL: '/dashboard/index/ping'
    };

    // State
    let lastActivityTime = Date.now();
    let warningShown = false;
    let countdownInterval = null;
    let pingInterval = null;

    /**
     * Reset activity timer when user interacts
     */
    function resetActivityTimer() {
        lastActivityTime = Date.now();

        // Hide warning if shown
        if (warningShown) {
            hideWarningModal();
        }
    }

    /**
     * Get time elapsed since last activity
     */
    function getIdleTime() {
        return Date.now() - lastActivityTime;
    }

    /**
     * Show warning modal with countdown
     */
    function showWarningModal() {
        if (warningShown) return;

        warningShown = true;

        // Create modal if it doesn't exist
        if (!document.getElementById('sessionTimeoutModal')) {
            createWarningModal();
        }

        // Show modal
        const modal = document.getElementById('sessionTimeoutModal');
        const backdrop = document.getElementById('sessionTimeoutBackdrop');

        modal.style.display = 'block';
        backdrop.style.display = 'block';

        // Add show class for animation
        setTimeout(() => {
            modal.classList.add('show');
            backdrop.classList.add('show');
        }, 10);

        // Start countdown
        startCountdown();

        // Play alert sound (optional)
        playAlertSound();
    }

    /**
     * Hide warning modal
     */
    function hideWarningModal() {
        warningShown = false;

        const modal = document.getElementById('sessionTimeoutModal');
        const backdrop = document.getElementById('sessionTimeoutBackdrop');

        if (modal && backdrop) {
            modal.classList.remove('show');
            backdrop.classList.remove('show');

            setTimeout(() => {
                modal.style.display = 'none';
                backdrop.style.display = 'none';
            }, 300);
        }

        // Stop countdown
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    }

    /**
     * Create warning modal HTML
     */
    function createWarningModal() {
        const modalHTML = `
            <!-- Session Timeout Backdrop -->
            <div id="sessionTimeoutBackdrop" class="session-timeout-backdrop"></div>

            <!-- Session Timeout Modal -->
            <div id="sessionTimeoutModal" class="session-timeout-modal">
                <div class="session-timeout-content">
                    <div class="session-timeout-header">
                        <i class="bi bi-clock-history session-timeout-icon"></i>
                        <h4>Session Timeout Warning</h4>
                    </div>

                    <div class="session-timeout-body">
                        <p class="session-timeout-message">
                            You have been inactive for a while. For security reasons,
                            you will be automatically logged out in:
                        </p>

                        <div class="session-timeout-countdown">
                            <div class="countdown-circle">
                                <span id="sessionTimeoutCountdown" class="countdown-time">2:00</span>
                            </div>
                        </div>

                        <p class="session-timeout-hint">
                            Click "Stay Logged In" to continue your session, or
                            "Logout Now" to logout immediately.
                        </p>
                    </div>

                    <div class="session-timeout-footer">
                        <button
                            type="button"
                            class="btn btn-secondary session-timeout-btn-logout"
                            onclick="sessionTimeout.logoutNow()">
                            <i class="bi bi-box-arrow-right"></i> Logout Now
                        </button>
                        <button
                            type="button"
                            class="btn btn-primary session-timeout-btn-stay"
                            onclick="sessionTimeout.stayLoggedIn()">
                            <i class="bi bi-check-circle"></i> Stay Logged In
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Add CSS
        const styleHTML = `
            <style>
                .session-timeout-backdrop {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.5);
                    z-index: 9998;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }

                .session-timeout-backdrop.show {
                    opacity: 1;
                }

                .session-timeout-modal {
                    display: none;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) scale(0.9);
                    z-index: 9999;
                    opacity: 0;
                    transition: all 0.3s ease;
                    max-width: 500px;
                    width: 90%;
                }

                .session-timeout-modal.show {
                    opacity: 1;
                    transform: translate(-50%, -50%) scale(1);
                }

                .session-timeout-content {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                    overflow: hidden;
                }

                .session-timeout-header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 20px;
                    text-align: center;
                }

                .session-timeout-icon {
                    font-size: 48px;
                    display: block;
                    margin-bottom: 10px;
                    animation: pulse 2s infinite;
                }

                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }

                .session-timeout-header h4 {
                    margin: 0;
                    font-size: 20px;
                    font-weight: 600;
                }

                .session-timeout-body {
                    padding: 30px 20px;
                    text-align: center;
                }

                .session-timeout-message {
                    color: #333;
                    font-size: 16px;
                    margin-bottom: 20px;
                    line-height: 1.5;
                }

                .session-timeout-countdown {
                    margin: 20px 0;
                }

                .countdown-circle {
                    display: inline-block;
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                }

                .countdown-time {
                    color: white;
                    font-size: 36px;
                    font-weight: bold;
                    font-family: 'Courier New', monospace;
                }

                .session-timeout-hint {
                    color: #666;
                    font-size: 14px;
                    margin-top: 20px;
                    line-height: 1.4;
                }

                .session-timeout-footer {
                    padding: 20px;
                    background: #f8f9fa;
                    display: flex;
                    gap: 10px;
                    justify-content: center;
                }

                .session-timeout-footer .btn {
                    padding: 10px 20px;
                    font-size: 14px;
                    border-radius: 5px;
                    border: none;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                }

                .session-timeout-btn-logout {
                    background: #6c757d;
                    color: white;
                }

                .session-timeout-btn-logout:hover {
                    background: #5a6268;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                }

                .session-timeout-btn-stay {
                    background: #667eea;
                    color: white;
                }

                .session-timeout-btn-stay:hover {
                    background: #5568d3;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
                }

                /* Responsive */
                @media (max-width: 576px) {
                    .session-timeout-modal {
                        width: 95%;
                    }

                    .session-timeout-footer {
                        flex-direction: column;
                    }

                    .session-timeout-footer .btn {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
        `;

        // Append to body
        document.body.insertAdjacentHTML('beforeend', styleHTML + modalHTML);
    }

    /**
     * Start countdown timer
     */
    function startCountdown() {
        const warningTime = CONFIG.WARNING_TIMEOUT;
        const startTime = Date.now();

        updateCountdownDisplay(warningTime);

        countdownInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            const remaining = warningTime - elapsed;

            if (remaining <= 0) {
                clearInterval(countdownInterval);
                performLogout('auto-logout');
            } else {
                updateCountdownDisplay(remaining);
            }
        }, 1000);
    }

    /**
     * Update countdown display
     */
    function updateCountdownDisplay(milliseconds) {
        const totalSeconds = Math.ceil(milliseconds / 1000);
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;

        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        const countdownElement = document.getElementById('sessionTimeoutCountdown');
        if (countdownElement) {
            countdownElement.textContent = display;

            // Change color when less than 30 seconds
            if (totalSeconds <= 30) {
                countdownElement.style.color = '#ff6b6b';
            }
        }
    }

    /**
     * Perform logout
     */
    function performLogout(reason = 'manual') {
        // Log the logout event
        logLogoutEvent(reason);

        // Redirect to logout URL
        window.location.href = CONFIG.LOGOUT_URL + '?reason=' + reason;
    }

    /**
     * Log logout event to Activity Logs
     */
    function logLogoutEvent(reason) {
        // Send async request to log the event
        const data = new FormData();
        data.append('reason', reason);
        data.append('timestamp', new Date().toISOString());

        // Use sendBeacon for reliability (works even if page is closing)
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/dashboard/index/logAutoLogout', data);
        } else {
            // Fallback to synchronous XHR
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/dashboard/index/logAutoLogout', false);
            xhr.send(data);
        }
    }

    /**
     * Ping server to keep session alive
     */
    function pingServer() {
        fetch(CONFIG.PING_URL, {
            method: 'GET',
            credentials: 'same-origin'
        }).catch(err => {
            console.error('Session ping failed:', err);
        });
    }

    /**
     * Play alert sound (optional)
     */
    function playAlertSound() {
        // Simple beep using Web Audio API
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            oscillator.frequency.value = 800;
            oscillator.type = 'sine';

            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.5);
        } catch (e) {
            // Silent fail - audio not critical
        }
    }

    /**
     * Check for idle timeout
     */
    function checkIdleTimeout() {
        const idleTime = getIdleTime();

        // Show warning if idle for 28 minutes
        if (idleTime >= CONFIG.IDLE_TIMEOUT && !warningShown) {
            showWarningModal();
        }

        // Auto-logout if idle for 30 minutes (should not reach here if modal works)
        if (idleTime >= CONFIG.TOTAL_TIMEOUT) {
            performLogout('auto-logout');
        }
    }

    /**
     * Initialize session timeout
     */
    function init() {
        // Track user activity
        const activityEvents = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        activityEvents.forEach(event => {
            document.addEventListener(event, resetActivityTimer, { passive: true });
        });

        // Start checking for idle timeout
        setInterval(checkIdleTimeout, CONFIG.CHECK_INTERVAL);

        // Start pinging server to keep session alive
        pingInterval = setInterval(pingServer, CONFIG.PING_INTERVAL);

        // Initial ping
        pingServer();

        console.log('Session timeout initialized: ' + (CONFIG.TOTAL_TIMEOUT / 60000) + ' minutes');
    }

    /**
     * Public API
     */
    window.sessionTimeout = {
        stayLoggedIn: function() {
            resetActivityTimer();
            pingServer();
        },

        logoutNow: function() {
            performLogout('manual');
        },

        resetTimer: function() {
            resetActivityTimer();
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
