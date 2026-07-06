<div id="loading-modal" class="loading-modal" role="alert" aria-live="assertive" aria-busy="true" aria-hidden="true">
    <div class="loading-modal__panel">
        <div class="loading-modal__spinner" aria-hidden="true"></div>
        <p id="loading-modal-message" class="loading-modal__message">処理中です。しばらくお待ちください。</p>
    </div>
</div>

<style>
    .loading-modal {
        align-items: center;
        background: rgba(15, 23, 42, 0.58);
        display: none;
        inset: 0;
        justify-content: center;
        padding: 1.5rem;
        position: fixed;
        z-index: 50;
    }

    .loading-modal.is-open {
        display: flex;
    }

    .loading-modal__panel {
        align-items: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.2);
        display: flex;
        flex-direction: column;
        gap: 1rem;
        max-width: 20rem;
        padding: 2rem;
        text-align: center;
        width: 100%;
    }

    .loading-modal__spinner {
        animation: loading-modal-spin 0.8s linear infinite;
        border: 4px solid #dbeafe;
        border-radius: 9999px;
        border-top-color: #2563eb;
        height: 3rem;
        width: 3rem;
    }

    .loading-modal__message {
        color: #334155;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.6;
        margin: 0;
    }

    body.loading-modal-open {
        overflow: hidden;
    }

    @keyframes loading-modal-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<script>
    (() => {
        const modal = document.getElementById('loading-modal');
        const message = document.getElementById('loading-modal-message');

        if (!modal || !message) {
            return;
        }

        const openLoadingModal = (text) => {
            message.textContent = text || '処理中です。しばらくお待ちください。';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('loading-modal-open');
        };

        document.querySelectorAll('form[data-loading-message]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.submitted === 'true') {
                    event.preventDefault();
                    return;
                }

                if (!form.checkValidity()) {
                    return;
                }

                form.dataset.submitted = 'true';
                form.querySelectorAll('button, input, textarea, select').forEach((field) => {
                    field.readOnly = true;

                    if (field.tagName === 'BUTTON' || field.type === 'submit') {
                        field.disabled = true;
                    }
                });
                openLoadingModal(form.dataset.loadingMessage);
            });
        });
    })();
</script>
