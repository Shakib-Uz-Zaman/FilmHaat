class PopupManager {
    constructor() {
        this.popupOverlay = null;
        this.hasShownPopup = false;
        this.timerInterval = null;
        this.init();
    }

    init() {
        if (this.hasShownPopup || sessionStorage.getItem('popupShown') === 'true') {
            return;
        }

        this.fetchPopupConfig();
    }

    async fetchPopupConfig() {
        try {
            const response = await fetch('config-api.php?action=get_popup_config');
            const data = await response.json();
            
            if (data.success && data.config) {
                const config = data.config;
                
                if (config.enabled && config.image_path && !config.hidden) {
                    this.showPopup(config);
                }
            }
        } catch (error) {
            console.error('Error loading popup config:', error);
        }
    }

    showPopup(config) {
        setTimeout(() => {
            this.createPopupElement(config);
            this.hasShownPopup = true;
            sessionStorage.setItem('popupShown', 'true');
            
            document.body.classList.add('popup-active');
            
            setTimeout(() => {
                if (this.popupOverlay) {
                    this.popupOverlay.classList.add('active');
                }
            }, 50);
        }, config.show_delay);
    }

    createPopupElement(config) {
        const overlay = document.createElement('div');
        overlay.className = 'popup-overlay';
        
        const container = document.createElement('div');
        container.className = 'popup-container';
        
        const closeBtn = document.createElement('button');
        closeBtn.className = 'popup-close-btn';
        closeBtn.innerHTML = '&times;';
        closeBtn.setAttribute('aria-label', 'Close popup');
        closeBtn.onclick = (e) => {
            e.stopPropagation();
            this.closePopup();
        };
        closeBtn.style.display = 'none';
        closeBtn.style.opacity = '0';
        
        const imageWrapper = document.createElement('div');
        imageWrapper.className = 'popup-image-wrapper';
        
        const timer = document.createElement('div');
        timer.className = 'popup-timer';
        const countdownDuration = config.countdown_duration || 10;
        timer.textContent = countdownDuration;
        timer.style.opacity = '0';
        
        const img = document.createElement('img');
        img.className = 'popup-image';
        img.src = config.image_path;
        img.alt = 'Popup promotional image';
        img.loading = 'eager';
        
        if (config.target_url) {
            imageWrapper.style.cursor = 'pointer';
            imageWrapper.onclick = () => {
                window.location.href = config.target_url;
            };
        }
        
        imageWrapper.appendChild(img);
        imageWrapper.appendChild(timer);
        imageWrapper.appendChild(closeBtn);
        container.appendChild(imageWrapper);
        overlay.appendChild(container);
        
        img.onload = () => {
            timer.style.opacity = '1';
            
            let timeLeft = countdownDuration;
            this.timerInterval = setInterval(() => {
                timeLeft--;
                timer.textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(this.timerInterval);
                    timer.style.opacity = '0';
                    setTimeout(() => {
                        timer.style.display = 'none';
                        closeBtn.style.display = 'block';
                        setTimeout(() => {
                            closeBtn.style.opacity = '1';
                        }, 10);
                    }, 300);
                }
            }, 1000);
        };
        
        overlay.onclick = (e) => {
            if (e.target === overlay) {
                this.closePopup();
            }
        };
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                this.closePopup();
            }
        });
        
        document.body.appendChild(overlay);
        this.popupOverlay = overlay;
    }

    closePopup() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        if (this.popupOverlay) {
            this.popupOverlay.classList.remove('active');
            document.body.classList.remove('popup-active');
            setTimeout(() => {
                if (this.popupOverlay && this.popupOverlay.parentNode) {
                    this.popupOverlay.parentNode.removeChild(this.popupOverlay);
                    this.popupOverlay = null;
                }
            }, 300);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new PopupManager();
    });
} else {
    new PopupManager();
}
