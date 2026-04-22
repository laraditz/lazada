@php
    $backUrl = config('lazada.home_url') ?? config('app.url');
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300&family=Plus+Jakarta+Sans:opsz,wght@8..18,400;8..18,500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<div class="lz-wrap">
    <div class="lz-blob lz-blob-a" aria-hidden="true"></div>
    <div class="lz-blob lz-blob-b" aria-hidden="true"></div>
    <div class="lz-blob lz-blob-c" aria-hidden="true"></div>

    <article class="lz-card">
        <header class="lz-header">
            <div class="lz-ring-wrap" aria-hidden="true">
                <svg class="lz-ring-svg" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="g1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%"   stop-color="#F57122"/>
                            <stop offset="100%" stop-color="#D0006F"/>
                        </linearGradient>
                    </defs>
                    <circle cx="36" cy="36" r="32" stroke="#1C1C2C" stroke-width="1.5"/>
                    <circle class="lz-ring-arc"   cx="36" cy="36" r="32" stroke="url(#g1)" stroke-width="1.5" stroke-linecap="round" transform="rotate(-90 36 36)"/>
                    <path   class="lz-ring-check" d="M22 36L31.5 45L50 27" stroke="url(#g1)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="lz-status">
                <span class="lz-status-dot"></span>
                <span class="lz-status-text">{{ __('AUTHORIZED') }}</span>
            </div>

            <h1 class="lz-heading">{{ __('Seller Connected') }}</h1>

            @if($seller?->accessToken)
            <p class="lz-sub">{{ __('Your access token is ready. You may now make Lazada API calls using this SDK.') }}</p>
            @endif
        </header>

        <div class="lz-divider" aria-hidden="true"></div>

        <dl class="lz-list">
            <div class="lz-row">
                <dt>{{ __('Auth Code') }}</dt>
                <dd class="lz-mono lz-trim" title="{{ $code }}">{{ $code }}</dd>
            </div>

            @if($seller)
                @if($seller->name)
                <div class="lz-row">
                    <dt>{{ __('Seller Name') }}</dt>
                    <dd>{{ $seller->name }}</dd>
                </div>
                @endif

                <div class="lz-row">
                    <dt>{{ __('Seller ID') }}</dt>
                    <dd class="lz-mono">{{ $seller->id }}</dd>
                </div>

                <div class="lz-row">
                    <dt>{{ __('Short Code') }}</dt>
                    <dd><span class="lz-tag">{{ $seller->short_code }}</span></dd>
                </div>

                @if($seller->accessToken)
                <div class="lz-row lz-row-block">
                    <dt>{{ __('Access Token') }}</dt>
                    <dd>
                        <div class="lz-tok">
                            <code class="lz-tok-val">{{ $seller->accessToken->access_token }}</code>
                            <button class="lz-copy" data-v="{{ $seller->accessToken->access_token }}" type="button" aria-label="{{ __('Copy access token') }}">
                                <svg class="ic-copy" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <svg class="ic-ok" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        </div>
                        <time class="lz-exp">{{ __('Expires') }} {{ $seller->accessToken->expires_at?->toDateTimeString() }}</time>
                    </dd>
                </div>

                <div class="lz-row lz-row-block lz-row-last">
                    <dt>{{ __('Refresh Token') }}</dt>
                    <dd>
                        <div class="lz-tok">
                            <code class="lz-tok-val">{{ $seller->accessToken->refresh_token }}</code>
                            <button class="lz-copy" data-v="{{ $seller->accessToken->refresh_token }}" type="button" aria-label="{{ __('Copy refresh token') }}">
                                <svg class="ic-copy" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <svg class="ic-ok" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        </div>
                        <time class="lz-exp">{{ __('Expires') }} {{ $seller->accessToken->refresh_expires_at?->toDateTimeString() }}</time>
                    </dd>
                </div>
                @endif
            @endif
        </dl>

        <div class="lz-footer">
            <a href="{{ $backUrl }}" class="lz-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                {{ __('Back to Home') }}
            </a>
        </div>
    </article>
</div>

<style>
/* --- Global reset --- */
body { margin: 0; padding: 0; }

/* --- Reset within scope --- */
.lz-wrap *, .lz-wrap *::before, .lz-wrap *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* --- Root & Variables --- */
.lz-wrap {
    --orange:      #F57122;
    --pink:        #D0006F;
    --blue:        #0071CE;
    --grad:        linear-gradient(135deg, #F57122 0%, #D0006F 100%);
    --bg:          #07070C;
    --surface:     #0B0B12;
    --border:      #1A1A28;
    --border-sub:  #131320;
    --text:        #F0EDE6;
    --muted:       #46465A;
    --font-head:   'Fraunces', Georgia, serif;
    --font-body:   'Plus Jakarta Sans', system-ui, sans-serif;
    --font-mono:   'JetBrains Mono', 'Courier New', monospace;

    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 20px;
    background: var(--bg);
    font-family: var(--font-body);
    color: var(--text);
    overflow: hidden;
}

/* --- Ambient gradient blobs --- */
.lz-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
}

.lz-blob-a {
    width: 560px;
    height: 400px;
    top: -120px;
    left: -80px;
    background: radial-gradient(ellipse, rgba(245, 113, 34, 0.18) 0%, transparent 70%);
}

.lz-blob-b {
    width: 480px;
    height: 380px;
    top: -100px;
    right: -80px;
    background: radial-gradient(ellipse, rgba(208, 0, 111, 0.14) 0%, transparent 70%);
}

.lz-blob-c {
    width: 600px;
    height: 360px;
    bottom: -140px;
    left: 50%;
    transform: translateX(-50%);
    background: radial-gradient(ellipse, rgba(0, 113, 206, 0.10) 0%, transparent 70%);
}

/* --- Card --- */
.lz-card {
    position: relative;
    width: 100%;
    max-width: 660px;
    background: var(--surface);
    border-radius: 20px;
    overflow: hidden;
    animation: lz-rise 0.9s cubic-bezier(0.16, 1, 0.3, 1) both;
    /* Gradient border via box-shadow layering */
    box-shadow:
        0 0 0 1px rgba(245, 113, 34, 0.25),
        0 0 60px rgba(245, 113, 34, 0.06),
        0 0 120px rgba(208, 0, 111, 0.04),
        0 32px 80px rgba(0, 0, 0, 0.5);
}

/* Gradient wash on card interior */
.lz-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        linear-gradient(145deg, rgba(245,113,34,0.04) 0%, transparent 40%),
        linear-gradient(225deg, rgba(208,0,111,0.03) 0%, transparent 40%);
    pointer-events: none;
    z-index: 0;
}

/* Top gradient hairline */
.lz-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, #F57122 30%, #D0006F 70%, transparent 100%);
    opacity: 0.7;
    z-index: 1;
}

/* --- Header --- */
.lz-header {
    position: relative;
    z-index: 1;
    padding: 52px 52px 0;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* --- Animated ring --- */
.lz-ring-wrap {
    width: 80px;
    height: 80px;
    margin-bottom: 28px;
}

.lz-ring-svg {
    width: 100%;
    height: 100%;
}

.lz-ring-arc {
    stroke-dasharray: 201;
    stroke-dashoffset: 201;
    animation: lz-draw 1.1s cubic-bezier(0.4, 0, 0.2, 1) forwards 0.3s;
}

.lz-ring-check {
    stroke-dasharray: 42;
    stroke-dashoffset: 42;
    animation: lz-draw 0.45s ease-out forwards 1.4s;
}

/* --- Status pill --- */
.lz-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 5px 14px;
    border-radius: 99px;
    border: 1px solid rgba(245, 113, 34, 0.3);
    background: linear-gradient(135deg, rgba(245,113,34,0.1), rgba(208,0,111,0.1));
    margin-bottom: 18px;
}

.lz-status-dot {
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--grad);
    background: linear-gradient(135deg, #F57122, #D0006F);
    animation: lz-pulse 2.5s ease infinite 2s;
}

.lz-status-text {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.14em;
    background: var(--grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* --- Heading --- */
.lz-heading {
    font-family: var(--font-head);
    font-size: clamp(38px, 6.5vw, 58px);
    font-weight: 300;
    font-style: italic;
    font-optical-sizing: auto;
    line-height: 1.05;
    letter-spacing: -0.01em;
    color: var(--text);
    margin-bottom: 14px;
}

/* --- Subtitle --- */
.lz-sub {
    font-size: 13.5px;
    line-height: 1.8;
    color: var(--muted);
    max-width: 340px;
    padding-bottom: 48px;
}

/* --- Divider --- */
.lz-divider {
    position: relative;
    z-index: 1;
    height: 1px;
    margin: 0 52px;
    background: linear-gradient(90deg, transparent, var(--border) 30%, var(--border) 70%, transparent);
}

/* --- Data list --- */
.lz-list {
    position: relative;
    z-index: 1;
    padding: 8px 52px 0;
}

/* --- Row --- */
.lz-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 20px;
    padding: 17px 0;
    border-bottom: 1px solid var(--border-sub);
    animation: lz-fade-up 0.4s ease both;
}

.lz-row:nth-child(1) { animation-delay: 0.12s; }
.lz-row:nth-child(2) { animation-delay: 0.18s; }
.lz-row:nth-child(3) { animation-delay: 0.24s; }
.lz-row:nth-child(4) { animation-delay: 0.30s; }
.lz-row:nth-child(5) { animation-delay: 0.36s; }
.lz-row:nth-child(6) { animation-delay: 0.42s; }

.lz-row:last-child   { border-bottom: none; }
.lz-row-last         { border-bottom: none; }

.lz-row-block {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
}

.lz-row dt {
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    letter-spacing: 0.11em;
    text-transform: uppercase;
    color: var(--muted);
    flex-shrink: 0;
}

.lz-row dd {
    font-size: 14px;
    color: var(--text);
    text-align: right;
    line-height: 1.5;
}

.lz-row-block dd {
    text-align: left;
}

/* --- Utilities --- */
.lz-mono {
    font-family: var(--font-mono);
    font-size: 13px;
}

.lz-trim {
    max-width: 280px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* --- Short code tag --- */
.lz-tag {
    display: inline-block;
    padding: 3px 11px;
    border-radius: 6px;
    border: 1px solid rgba(245, 113, 34, 0.28);
    background: linear-gradient(135deg, rgba(245,113,34,0.12), rgba(208,0,111,0.12));
    font-family: var(--font-mono);
    font-size: 12px;
    color: #F57122;
    letter-spacing: 0.06em;
}

/* --- Token box (blue-tinted) --- */
.lz-tok {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(0, 113, 206, 0.06);
    border: 1px solid rgba(0, 113, 206, 0.18);
    border-radius: 8px;
    padding: 10px 14px;
}

.lz-tok-val {
    font-family: var(--font-mono);
    font-size: 12px;
    color: #7899B8;
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* --- Copy button --- */
.lz-copy {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border: 1px solid rgba(0, 113, 206, 0.2);
    border-radius: 6px;
    background: transparent;
    color: var(--muted);
    cursor: pointer;
    transition: color 0.18s, border-color 0.18s, background 0.18s;
}

.lz-copy:hover {
    color: var(--blue);
    border-color: rgba(0, 113, 206, 0.4);
    background: rgba(0, 113, 206, 0.1);
}

.lz-copy .ic-ok { display: none; }

.lz-copy.--ok .ic-copy { display: none; }
.lz-copy.--ok .ic-ok   { display: block; }
.lz-copy.--ok {
    color: #3ECF8E;
    border-color: rgba(62, 207, 142, 0.3);
    background: rgba(62, 207, 142, 0.08);
}

/* --- Expiry timestamp --- */
.lz-exp {
    display: block;
    margin-top: 9px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: rgba(0, 113, 206, 0.5);
}

/* --- Footer with back button --- */
.lz-footer {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    padding: 36px 52px 48px;
    border-top: 1px solid var(--border-sub);
    margin-top: 8px;
}

.lz-back {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 13px 32px;
    border-radius: 10px;
    background: linear-gradient(135deg, #F57122 0%, #D0006F 100%);
    color: #fff;
    font-family: var(--font-body);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    letter-spacing: 0.01em;
    transition: opacity 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 20px rgba(245, 113, 34, 0.3);
}

.lz-back:hover {
    opacity: 0.92;
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(245, 113, 34, 0.4);
}

.lz-back:active {
    transform: translateY(0);
    opacity: 1;
}

/* --- Keyframes --- */
@keyframes lz-rise {
    from { opacity: 0; transform: translateY(24px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0)   scale(1); }
}

@keyframes lz-fade-up {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

@keyframes lz-draw {
    to { stroke-dashoffset: 0; }
}

@keyframes lz-pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
}

/* --- Responsive --- */
@media (max-width: 600px) {
    .lz-header  { padding: 36px 24px 0; }
    .lz-divider { margin: 0 24px; }
    .lz-list    { padding: 8px 24px 0; }
    .lz-footer  { padding: 28px 24px 36px; }
    .lz-trim    { max-width: 160px; }
    .lz-row     { flex-direction: column; gap: 6px; }
    .lz-row dd  { text-align: left; }
    .lz-back    { width: 100%; justify-content: center; }
}
</style>

<script>
document.querySelectorAll('.lz-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(btn.dataset.v).then(function () {
            btn.classList.add('--ok');
            setTimeout(function () { btn.classList.remove('--ok'); }, 2000);
        });
    });
});
</script>
