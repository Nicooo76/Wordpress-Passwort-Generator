<?php
/**
 * Plugin Name: Passwort Generator
 * Plugin URI: https://gauditz.com/
 * Description: Ein sicherer und benutzerfreundlicher Passwort-Generator für WordPress mit Stärke-Anzeige und moderner UI.
 * Version: 2.0
 * Author: Sven Gauditz
 * Author URI: https://gauditz.com/
 * License: GPL v2 or later
 * Text Domain: passwort-generator
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin-Konstanten
define('PWG_VERSION', '2.0');
define('PWG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PWG_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Shortcode für den Passwort-Generator
 */
function pwg_password_generator_shortcode() {
    wp_enqueue_style(
        'pwg-generator-style',
        PWG_PLUGIN_URL . 'assets/css/style.css',
        array(),
        PWG_VERSION
    );

    ob_start();
    ?>
    <div id="pwg-generator" class="pwg-container" role="region" aria-label="<?php echo esc_attr__('Passwort-Generator', 'passwort-generator'); ?>">
        <div class="pwg-card">
            <h3 class="pwg-title"><?php esc_html_e('Sicheres Passwort erstellen', 'passwort-generator'); ?></h3>

            <!-- Passwort-Anzeige -->
            <div class="pwg-output-wrapper">
                <div class="pwg-output-group">
                    <input type="password" id="pwg-output" class="pwg-output" readonly
                           aria-label="<?php esc_attr_e('Generiertes Passwort', 'passwort-generator'); ?>"
                           autocomplete="off">
                    <button type="button" id="pwg-toggle-visibility" class="pwg-icon-btn" title="<?php esc_attr_e('Passwort ein-/ausblenden', 'passwort-generator'); ?>"
                            aria-label="<?php esc_attr_e('Passwort ein- oder ausblenden', 'passwort-generator'); ?>">👁</button>
                    <button type="button" id="pwg-copy" class="pwg-btn pwg-btn-primary">
                        <?php esc_html_e('Kopieren', 'passwort-generator'); ?>
                    </button>
                </div>
                <div id="pwg-strength-meter" class="pwg-strength-meter" aria-hidden="true">
                    <div id="pwg-strength-bar" class="pwg-strength-bar"></div>
                    <span id="pwg-strength-label" class="pwg-strength-label"></span>
                </div>
            </div>

            <!-- Optionen -->
            <div class="pwg-options">
                <div class="pwg-option-row">
                    <label for="pwg-length" class="pwg-label">
                        <?php esc_html_e('Länge:', 'passwort-generator'); ?>
                        <span id="pwg-length-value" class="pwg-value">16</span>
                    </label>
                    <input type="range" id="pwg-length" class="pwg-range" min="8" max="64" value="16"
                           aria-describedby="pwg-length-value">
                </div>

                <div class="pwg-checkboxes">
                    <label class="pwg-checkbox">
                        <input type="checkbox" id="pwg-uppercase" checked>
                        <span><?php esc_html_e('Großbuchstaben (A-Z)', 'passwort-generator'); ?></span>
                    </label>
                    <label class="pwg-checkbox">
                        <input type="checkbox" id="pwg-lowercase" checked>
                        <span><?php esc_html_e('Kleinbuchstaben (a-z)', 'passwort-generator'); ?></span>
                    </label>
                    <label class="pwg-checkbox">
                        <input type="checkbox" id="pwg-numbers" checked>
                        <span><?php esc_html_e('Zahlen (0-9)', 'passwort-generator'); ?></span>
                    </label>
                    <label class="pwg-checkbox">
                        <input type="checkbox" id="pwg-special" checked>
                        <span><?php esc_html_e('Sonderzeichen (!@#$%^&*...)', 'passwort-generator'); ?></span>
                    </label>
                    <label class="pwg-checkbox">
                        <input type="checkbox" id="pwg-avoid-ambiguous">
                        <span><?php esc_html_e('Mehrdeutige Zeichen vermeiden (0, O, l, 1, I)', 'passwort-generator'); ?></span>
                    </label>
                </div>

                <div class="pwg-option-row pwg-special-count-row" id="pwg-special-row">
                    <label for="pwg-special-count" class="pwg-label">
                        <?php esc_html_e('Min. Sonderzeichen:', 'passwort-generator'); ?>
                        <span id="pwg-special-value" class="pwg-value">2</span>
                    </label>
                    <input type="range" id="pwg-special-count" class="pwg-range" min="0" max="10" value="2"
                           aria-describedby="pwg-special-value">
                </div>
            </div>

            <button type="button" id="pwg-generate" class="pwg-btn pwg-btn-generate">
                <?php esc_html_e('Neues Passwort generieren', 'passwort-generator'); ?>
            </button>
        </div>

        <!-- Toast-Benachrichtigung -->
        <div id="pwg-toast" class="pwg-toast" role="status" aria-live="polite" aria-atomic="true"></div>
    </div>

    <script>
    (function() {
        const PWG = {
            chars: {
                uppercase: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                lowercase: 'abcdefghijklmnopqrstuvwxyz',
                numbers: '0123456789',
                special: '!@#$%^&*()_+-=[]{}|;:,.<>?',
                specialSafe: '!@#$%^&*()_+-=[]{}|;:,.<>?',
                ambiguous: '0O1lI'
            },

            getSecureRandom(max) {
                const array = new Uint32Array(1);
                crypto.getRandomValues(array);
                return array[0] % max;
            },

            shuffleArray(arr) {
                for (let i = arr.length - 1; i > 0; i--) {
                    const j = this.getSecureRandom(i + 1);
                    [arr[i], arr[j]] = [arr[j], arr[i]];
                }
                return arr;
            },

            generate() {
                const length = parseInt(document.getElementById('pwg-length').value, 10);
                const useUppercase = document.getElementById('pwg-uppercase').checked;
                const useLowercase = document.getElementById('pwg-lowercase').checked;
                const useNumbers = document.getElementById('pwg-numbers').checked;
                const useSpecial = document.getElementById('pwg-special').checked;
                const avoidAmbiguous = document.getElementById('pwg-avoid-ambiguous').checked;
                let specialMin = parseInt(document.getElementById('pwg-special-count').value, 10);

                if (!useUppercase && !useLowercase && !useNumbers && !useSpecial) {
                    this.showToast('<?php echo esc_js(__('Bitte mindestens eine Zeichenart auswählen!', 'passwort-generator')); ?>', 'error');
                    return '';
                }

                specialMin = Math.min(specialMin, useSpecial ? Math.min(length, 10) : 0);

                let upper = this.chars.uppercase;
                let lower = this.chars.lowercase;
                let num = this.chars.numbers;
                let spec = this.chars.special;

                if (avoidAmbiguous) {
                    const u = upper.replace(/[OI]/g, '');
                    const l = lower.replace(/[l]/g, '');
                    const n = num.replace(/[01]/g, '');
                    const s = spec.replace(/[|]/g, '');
                    upper = u || upper;
                    lower = l || lower;
                    num = n || num;
                    spec = s || spec;
                }

                const pools = [];
                const password = [];

                if (useUppercase && upper) { pools.push({ chars: upper, min: 1 }); }
                if (useLowercase && lower) { pools.push({ chars: lower, min: 1 }); }
                if (useNumbers && num) { pools.push({ chars: num, min: 1 }); }
                if (useSpecial && spec) { pools.push({ chars: spec, min: specialMin }); }

                let totalMin = pools.reduce((s, p) => s + p.min, 0);
                if (totalMin > length) {
                    this.showToast('<?php echo esc_js(__('Länge erhöhen oder weniger Sonderzeichen wählen.', 'passwort-generator')); ?>', 'error');
                    return '';
                }

                pools.forEach(p => {
                    for (let i = 0; i < p.min; i++) {
                        password.push(p.chars[this.getSecureRandom(p.chars.length)]);
                    }
                });

                let allChars = '';
                pools.forEach(p => { allChars += p.chars; });

                while (password.length < length) {
                    password.push(allChars[this.getSecureRandom(allChars.length)]);
                }

                return this.shuffleArray(password).join('');
            },

            updateStrength(password) {
                const meter = document.getElementById('pwg-strength-meter');
                const bar = document.getElementById('pwg-strength-bar');
                const label = document.getElementById('pwg-strength-label');

                if (!password) {
                    meter.classList.remove('pwg-strength-weak', 'pwg-strength-fair', 'pwg-strength-good', 'pwg-strength-strong');
                    bar.style.width = '0%';
                    label.textContent = '';
                    return;
                }

                let score = 0;
                if (password.length >= 8) score += 1;
                if (password.length >= 12) score += 1;
                if (password.length >= 16) score += 1;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score += 1;
                if (/\d/.test(password)) score += 1;
                if (/[^A-Za-z0-9]/.test(password)) score += 1;

                const levels = ['', '<?php echo esc_js(__('Schwach', 'passwort-generator')); ?>', '<?php echo esc_js(__('Ausreichend', 'passwort-generator')); ?>', '<?php echo esc_js(__('Gut', 'passwort-generator')); ?>', '<?php echo esc_js(__('Stark', 'passwort-generator')); ?>'];
                const widths = ['0%', '25%', '50%', '75%', '100%'];
                const classes = ['', 'pwg-strength-weak', 'pwg-strength-fair', 'pwg-strength-good', 'pwg-strength-strong'];

                meter.className = 'pwg-strength-meter ' + classes[score];
                bar.style.width = widths[score];
                label.textContent = levels[score];
            },

            showToast(message, type = 'success') {
                const toast = document.getElementById('pwg-toast');
                toast.textContent = message;
                toast.className = 'pwg-toast pwg-toast-show pwg-toast-' + type;
                clearTimeout(PWG.toastTimer);
                PWG.toastTimer = setTimeout(() => {
                    toast.classList.remove('pwg-toast-show');
                }, 3000);
            },

            init() {
                const self = this;
                const output = document.getElementById('pwg-output');
                const generateBtn = document.getElementById('pwg-generate');
                const copyBtn = document.getElementById('pwg-copy');
                const toggleBtn = document.getElementById('pwg-toggle-visibility');
                const lengthInput = document.getElementById('pwg-length');
                const specialInput = document.getElementById('pwg-special-count');
                const specialCheck = document.getElementById('pwg-special');

                const updateLengthDisplay = () => {
                    document.getElementById('pwg-length-value').textContent = lengthInput.value;
                };
                const updateSpecialDisplay = () => {
                    document.getElementById('pwg-special-value').textContent = specialInput.value;
                    document.getElementById('pwg-special-row').style.opacity = specialCheck.checked ? '1' : '0.5';
                };

                lengthInput.addEventListener('input', updateLengthDisplay);
                specialInput.addEventListener('input', updateSpecialDisplay);
                specialCheck.addEventListener('change', updateSpecialDisplay);

                const doGenerate = () => {
                    const pw = self.generate();
                    if (pw) {
                        output.value = pw;
                        self.updateStrength(pw);
                        const isPassword = output.type === 'password';
                        output.type = isPassword ? 'password' : 'text';
                    }
                };

                generateBtn.addEventListener('click', doGenerate);

                copyBtn.addEventListener('click', () => {
                    if (!output.value) {
                        doGenerate();
                        if (!output.value) return;
                    }
                    navigator.clipboard.writeText(output.value).then(() => {
                        self.showToast('<?php echo esc_js(__('Passwort in die Zwischenablage kopiert!', 'passwort-generator')); ?>');
                    }).catch(() => {
                        output.select();
                        document.execCommand('copy');
                        self.showToast('<?php echo esc_js(__('Passwort kopiert (Fallback)!', 'passwort-generator')); ?>');
                    });
                });

                toggleBtn.addEventListener('click', () => {
                    output.type = output.type === 'password' ? 'text' : 'password';
                    toggleBtn.textContent = output.type === 'password' ? '👁' : '🙈';
                });

                output.addEventListener('input', () => self.updateStrength(output.value));
                output.addEventListener('focus', () => output.select());

                updateLengthDisplay();
                updateSpecialDisplay();
                doGenerate();
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => PWG.init());
        } else {
            PWG.init();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('passwort_generator', 'pwg_password_generator_shortcode');

/**
 * Sprachdateien laden
 */
function pwg_load_textdomain() {
    load_plugin_textdomain('passwort-generator', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'pwg_load_textdomain');
