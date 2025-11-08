// Navigation Icon Switcher - Active/Inactive States
(function() {
    const iconMapping = {
        'home': 'fi-ss-house-crack',
        'latest': 'fi-ss-flame',
        'loved': 'fi-ss-heart',
        'about': 'fi-sr-info'
    };

    function updateNavigationIcons() {
        const navItems = document.querySelectorAll('.nav-link, .bottom-nav-item');
        
        navItems.forEach(item => {
            const page = item.getAttribute('data-page');
            const icon = item.querySelector('i');
            
            if (icon && iconMapping[page]) {
                icon.className = 'fi ' + iconMapping[page];
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateNavigationIcons);
    } else {
        updateNavigationIcons();
    }
})();

// Weekly Top 10 Tracking System (Last 7 Days) - Server-side with all users data
const API_WEEKLY_TOP10 = 'api-weekly-top10.php';

// Track if Weekly Top 10 has movies
let hasWeeklyTop10Movies = false;

async function trackMovieView(title, link, image, language) {
    if (!title || !link) {
        console.warn('trackMovieView: Missing title or link');
        return { success: false, error: 'Missing required fields' };
    }
    
    try {
        const response = await fetch(API_WEEKLY_TOP10, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'track',
                title: title,
                link: link,
                image: image,
                language: language
            })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            console.log('View tracked successfully:', title.substring(0, 50));
        } else {
            console.error('Failed to track view:', result.error);
        }
        
        return result;
    } catch (error) {
        console.error('Error tracking movie view:', error.message, '- Title:', title.substring(0, 50));
        return { success: false, error: error.message };
    }
}

async function getWeeklyTop10() {
    try {
        const response = await fetch(`${API_WEEKLY_TOP10}?action=top10`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const top10 = await response.json();
        return Array.isArray(top10) ? top10 : [];
    } catch (error) {
        console.error('Error getting weekly top 10:', error);
        return [];
    }
}

function saveToRecentViewed(title, link, image, language, genre, website, skipReload = false) {
    if (!title || !link) {
        return;
    }
    
    try {
        let recentViewedMovies = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
        
        const existingIndex = recentViewedMovies.findIndex(movie => movie.title === title && movie.link === link);
        if (existingIndex !== -1) {
            recentViewedMovies.splice(existingIndex, 1);
        }
        
        const movieData = {
            title: title,
            link: link,
            image: image || '',
            language: language || '',
            genre: genre || '',
            website: website || '',
            viewedAt: new Date().toISOString()
        };
        
        recentViewedMovies.unshift(movieData);
        
        const maxRecentMovies = 20;
        if (recentViewedMovies.length > maxRecentMovies) {
            recentViewedMovies = recentViewedMovies.slice(0, maxRecentMovies);
        }
        
        localStorage.setItem('recentViewedMovies', JSON.stringify(recentViewedMovies));
        
        if (!skipReload && typeof window.loadRecentViewedMovies === 'function') {
            window.loadRecentViewedMovies();
        }
    } catch (error) {
        console.warn('Failed to save to recent viewed:', error);
    }
}

function getRelativeTime(timestamp) {
    if (!timestamp) return '';
    
    const now = new Date();
    const viewedDate = new Date(timestamp);
    
    // Reset time to start of day for accurate day comparison
    now.setHours(0, 0, 0, 0);
    viewedDate.setHours(0, 0, 0, 0);
    
    const diffTime = now - viewedDate;
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else {
        return `${diffDays} Days Ago`;
    }
}

let currentThemeColor = '#000000';
let targetThemeColor = '#000000';
let isTransitioning = false;

function handleScroll() {
    const navbar = document.querySelector('.navbar');
    const themeColorMeta = document.getElementById('themeColor');
    
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
            
            // Only update to black when scrolling down
            if (themeColorMeta) {
                const currentColor = themeColorMeta.getAttribute('content');
                if (currentColor !== '#000000' && !isTransitioning) {
                    isTransitioning = true;
                    smoothTransitionThemeColor(themeColorMeta, currentColor, '#000000', 300);
                }
            }
        } else {
            navbar.classList.remove('scrolled');
            // Don't set theme color here - let carousel handle it for smooth transition
        }
    }
}

window.addEventListener('scroll', handleScroll);

// Check scroll position immediately on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', handleScroll);
} else {
    handleScroll();
}

function smoothTransitionThemeColor(metaElement, startColor, endColor, duration) {
    const startTime = performance.now();
    const start = hexToRgb(startColor);
    const end = hexToRgb(endColor);
    
    function animate(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const easeProgress = progress < 0.5 
            ? 2 * progress * progress 
            : 1 - Math.pow(-2 * progress + 2, 2) / 2;
        
        const r = Math.round(start.r + (end.r - start.r) * easeProgress);
        const g = Math.round(start.g + (end.g - start.g) * easeProgress);
        const b = Math.round(start.b + (end.b - start.b) * easeProgress);
        
        const color = `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
        metaElement.setAttribute('content', color);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        } else {
            currentThemeColor = endColor;
            isTransitioning = false;
        }
    }
    
    requestAnimationFrame(animate);
}

function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : { r: 0, g: 0, b: 0 };
}

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const clearBtn = document.getElementById('clearBtn');
    const filterBtn = document.getElementById('filterBtn');
    const filterDropdown = document.getElementById('filterDropdown');
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox');
    const resultsContainer = document.getElementById('resultsContainer');
    const results = document.getElementById('results');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    const animatedPlaceholder = document.getElementById('animatedPlaceholder');
    const placeholderText = animatedPlaceholder.querySelector('.placeholder-text');
    
    const navSearchInput = document.getElementById('navSearchInput');
    const navSearchBtn = document.getElementById('navSearchBtn');
    const navSearch = document.querySelector('.nav-search');
    const navAnimatedPlaceholder = document.getElementById('navAnimatedPlaceholder');
    const navPlaceholderText = navAnimatedPlaceholder ? navAnimatedPlaceholder.querySelector('.nav-placeholder-text') : null;

    const searchPopupModal = document.getElementById('searchPopupModal');
    const searchPopupClose = document.getElementById('searchPopupClose');

    let selectedWebsites = Array.from(filterCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
    let originalThemeColor = null;
    let previousActiveNavItem = null;

    function openSearchPopup() {
        if (searchPopupModal) {
            const themeColorMeta = document.getElementById('themeColor');
            if (themeColorMeta) {
                originalThemeColor = themeColorMeta.getAttribute('content');
                themeColorMeta.setAttribute('content', '#000000');
            }
            
            const bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                bottomNav.classList.add('hidden');
            }
            
            searchPopupModal.style.display = 'block';
            setTimeout(() => {
                searchPopupModal.classList.add('show');
            }, 10);
            document.body.style.overflow = 'hidden';
            if (searchInput) {
                setTimeout(() => {
                    searchInput.focus();
                }, 300);
            }
            
            loadRecentViewedMovies();
        }
    }

    function closeSearchPopup() {
        if (searchPopupModal) {
            const themeColorMeta = document.getElementById('themeColor');
            if (themeColorMeta && originalThemeColor) {
                themeColorMeta.setAttribute('content', originalThemeColor);
            }
            
            const bottomNav = document.querySelector('.bottom-nav');
            if (bottomNav) {
                bottomNav.classList.remove('hidden');
            }
            
            const bottomNavSearch = document.getElementById('bottomNavSearch');
            if (bottomNavSearch && previousActiveNavItem) {
                bottomNavSearch.classList.remove('active');
                previousActiveNavItem.classList.add('active');
            }
            
            searchPopupModal.classList.remove('show');
            setTimeout(() => {
                searchPopupModal.style.display = 'none';
                document.body.style.overflow = 'auto';
                
                const categoriesSection = document.getElementById('categoriesSection');
                
                if (categoriesSection) categoriesSection.style.display = 'block';
            }, 300);
        }
    }

    let recentViewedItemsPerPage = 12;
    let recentViewedShowingAll = false;

    function loadRecentViewedMovies(showAll = false) {
        const recentViewedContainer = document.getElementById('recentViewedContainer');
        const recentViewedGrid = document.getElementById('recentViewedGrid');
        
        if (!recentViewedGrid) return;
        
        let recentViewedMovies = [];
        try {
            recentViewedMovies = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
        } catch (error) {
            console.warn('Failed to load recent viewed movies:', error);
            recentViewedMovies = [];
        }
        
        if (recentViewedMovies.length === 0) {
            if (recentViewedContainer) {
                recentViewedContainer.style.display = 'none';
            }
            return;
        }
        
        if (recentViewedContainer) {
            recentViewedContainer.style.display = 'block';
        }
        
        recentViewedGrid.innerHTML = '';
        
        const itemsToShow = showAll ? recentViewedMovies.length : Math.min(recentViewedItemsPerPage, recentViewedMovies.length);
        const hasMoreItems = recentViewedMovies.length > recentViewedItemsPerPage;
        
        for (let i = 0; i < itemsToShow; i++) {
            const movie = recentViewedMovies[i];
            const index = i;
            
            const languageTag = movie.language ? `<span class="category-genre-tag">${escapeHtml(movie.language)}</span>` : '';
            const websiteTag = movie.website ? `<span class="category-genre-tag category-website-tag">${escapeHtml(movie.website)}</span>` : '';
            const viewedTimeTag = movie.viewedAt ? `<span class="category-genre-tag category-loved-time-tag">${getRelativeTime(movie.viewedAt)}</span>` : '';
            
            const tagsHtml = languageTag + websiteTag + viewedTimeTag || '<span class="category-genre-tag" style="opacity: 0.5;">No Info</span>';
            
            const imageUrl = movie.image || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23333%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2216%22%3ENo Image%3C/text%3E%3C/svg%3E';
            
            const movieHtml = `
                <div class="category-grid-item recent-viewed-item" 
                     data-title="${escapeHtml(movie.title)}"
                     data-link="${escapeHtml(movie.link || '#')}"
                     data-image="${escapeHtml(movie.image || '')}"
                     data-language="${escapeHtml(movie.language || '')}"
                     data-genre="${escapeHtml(movie.genre || '')}"
                     data-website="${escapeHtml(movie.website || '')}">
                    <div class="category-movie-image">
                        <img src="${escapeHtml(imageUrl)}" 
                             alt="${escapeHtml(movie.title)}" 
                             class="lazy-image"
                             loading="lazy"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23333%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2216%22%3ENo Image%3C/text%3E%3C/svg%3E'">
                    </div>
                    <div class="category-movie-info">
                        <h3 class="category-movie-title">${escapeHtml(movie.title)}</h3>
                        <div class="category-movie-genres">
                            ${tagsHtml}
                        </div>
                    </div>
                    <div class="category-movie-index">${String(index + 1).padStart(2, '0')}</div>
                </div>
            `;
            
            recentViewedGrid.innerHTML += movieHtml;
        }
        
        if (hasMoreItems && !showAll) {
            const loadMoreHtml = `
                <div class="category-next-card" id="recentViewedLoadMore">
                    <span class="category-next-card-text">Load More</span>
                </div>
            `;
            recentViewedGrid.innerHTML += loadMoreHtml;
            
            const loadMoreCard = document.getElementById('recentViewedLoadMore');
            if (loadMoreCard) {
                if (recentViewedMovies.length > 0) {
                    const randomMovie = recentViewedMovies[Math.floor(Math.random() * Math.min(recentViewedMovies.length, itemsToShow))];
                    if (randomMovie && randomMovie.image) {
                        loadMoreCard.style.backgroundImage = `url('${randomMovie.image}')`;
                    }
                }
                
                loadMoreCard.addEventListener('click', function() {
                    const currentItemCount = recentViewedGrid.querySelectorAll('.recent-viewed-item').length;
                    recentViewedShowingAll = true;
                    loadRecentViewedMovies(true);
                    
                    setTimeout(() => {
                        const allItems = recentViewedGrid.querySelectorAll('.recent-viewed-item');
                        for (let i = currentItemCount; i < allItems.length; i++) {
                            allItems[i].classList.add('new-item');
                        }
                        
                        setTimeout(() => {
                            const newItems = recentViewedGrid.querySelectorAll('.new-item');
                            newItems.forEach(item => item.classList.remove('new-item'));
                        }, 3000);
                    }, 50);
                });
            }
        }
        
        const movieItems = recentViewedGrid.querySelectorAll('.recent-viewed-item:not([data-click-initialized])');
        movieItems.forEach(item => {
            item.setAttribute('data-click-initialized', 'true');
            
            let touchStartX = 0;
            let touchStartY = 0;
            let touchStartTime = 0;
            let longPressTimer = null;
            let isSwiping = false;
            let isLongPress = false;
            let currentX = 0;
            let hasScrolled = false;
            
            function deleteRecentViewedItem(element) {
                const title = element.getAttribute('data-title');
                const link = element.getAttribute('data-link');
                
                let recentViewedMovies = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
                recentViewedMovies = recentViewedMovies.filter(movie => 
                    !(movie.title === title && movie.link === link)
                );
                localStorage.setItem('recentViewedMovies', JSON.stringify(recentViewedMovies));
                
                element.style.transform = 'translateX(-100%)';
                element.style.opacity = '0';
                
                setTimeout(() => {
                    loadRecentViewedMovies(recentViewedShowingAll);
                }, 300);
            }
            
            item.addEventListener('touchstart', function(e) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                touchStartTime = Date.now();
                isSwiping = false;
                isLongPress = false;
                currentX = 0;
                hasScrolled = false;
                
                longPressTimer = setTimeout(() => {
                    isLongPress = true;
                    navigator.vibrate && navigator.vibrate(50);
                    
                    const element = this;
                    const movieTitle = element.getAttribute('data-title');
                    showConfirmDialog(
                        'Remove from watch history?',
                        `"${movieTitle}" will be removed from your recent viewed list.`,
                        function() {
                            deleteRecentViewedItem(element);
                        }
                    );
                }, 500);
            }, { passive: true });
            
            item.addEventListener('touchmove', function(e) {
                if (isLongPress) {
                    clearTimeout(longPressTimer);
                    return;
                }
                
                const touchX = e.touches[0].clientX;
                const touchY = e.touches[0].clientY;
                const deltaX = touchX - touchStartX;
                const deltaY = touchY - touchStartY;
                
                if (Math.abs(deltaX) > 10 || Math.abs(deltaY) > 10) {
                    clearTimeout(longPressTimer);
                }
                
                if (Math.abs(deltaY) > 10) {
                    hasScrolled = true;
                }
                
                if (!isSwiping && Math.abs(deltaX) > Math.abs(deltaY) && deltaX < 0) {
                    isSwiping = true;
                }
                
                if (isSwiping) {
                    currentX = Math.min(0, deltaX);
                    
                    const opacity = Math.max(0.3, 1 - (Math.abs(currentX) / 200));
                    this.style.transform = `translateX(${currentX}px)`;
                    this.style.opacity = opacity;
                    this.style.transition = 'none';
                }
            }, { passive: true });
            
            item.addEventListener('touchend', function(e) {
                clearTimeout(longPressTimer);
                
                if (isLongPress) {
                    return;
                }
                
                if (isSwiping) {
                    const finalPosition = currentX;
                    
                    if (finalPosition < -100) {
                        deleteRecentViewedItem(this);
                    } else {
                        this.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
                        this.style.transform = 'translateX(0)';
                        this.style.opacity = '1';
                        
                        setTimeout(() => {
                            this.style.transition = '';
                        }, 300);
                    }
                } else if (!isLongPress && !hasScrolled && Date.now() - touchStartTime < 300) {
                    openMovieModal(this);
                }
            }, { passive: true });
            
            item.addEventListener('click', function(e) {
                if (!('ontouchstart' in window)) {
                    openMovieModal(this);
                }
            });
        });
        
        initializeLazyImages();
    }
    
    function showConfirmDialog(title, message, onConfirm) {
        const overlay = document.getElementById('confirmDialog');
        const titleEl = document.getElementById('confirmDialogTitle');
        const messageEl = document.getElementById('confirmDialogMessage');
        const confirmBtn = document.getElementById('confirmDialogConfirm');
        const cancelBtn = document.getElementById('confirmDialogCancel');
        
        if (!overlay) return;
        
        titleEl.textContent = title;
        messageEl.textContent = message;
        
        overlay.style.display = 'flex';
        setTimeout(() => overlay.classList.add('show'), 10);
        
        function closeDialog() {
            overlay.classList.remove('show');
            setTimeout(() => {
                overlay.style.display = 'none';
            }, 200);
            confirmBtn.onclick = null;
            cancelBtn.onclick = null;
            overlay.onclick = null;
        }
        
        confirmBtn.onclick = function() {
            closeDialog();
            if (onConfirm) onConfirm();
        };
        
        cancelBtn.onclick = closeDialog;
        
        overlay.onclick = function(e) {
            if (e.target === overlay) {
                closeDialog();
            }
        };
    }
    
    const clearRecentViewedBtn = document.getElementById('clearRecentViewedBtn');
    if (clearRecentViewedBtn) {
        clearRecentViewedBtn.addEventListener('click', function() {
            showConfirmDialog(
                'Clear watch history?',
                'This will remove all recently viewed items from your history.',
                function() {
                    localStorage.removeItem('recentViewedMovies');
                    const recentViewedContainer = document.getElementById('recentViewedContainer');
                    if (recentViewedContainer) {
                        recentViewedContainer.style.display = 'none';
                    }
                    const recentViewedGrid = document.getElementById('recentViewedGrid');
                    if (recentViewedGrid) {
                        recentViewedGrid.innerHTML = '';
                    }
                }
            );
        });
    }
    
    window.loadRecentViewedMovies = loadRecentViewedMovies;

    if (navSearch) {
        navSearch.addEventListener('click', openSearchPopup);
    }

    // Also attach to header menu buttons (for loved.php, more.php, latest.php pages)
    const headerMenuButtons = document.querySelectorAll('.header-menu-button');
    headerMenuButtons.forEach(button => {
        button.addEventListener('click', openSearchPopup);
    });

    if (navSearchInput) {
        navSearchInput.addEventListener('focus', openSearchPopup);
    }

    if (searchPopupClose) {
        searchPopupClose.addEventListener('click', closeSearchPopup);
    }

    if (searchPopupModal) {
        searchPopupModal.addEventListener('click', function(e) {
            if (e.target === searchPopupModal) {
                closeSearchPopup();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchPopupModal && searchPopupModal.classList.contains('show')) {
            closeSearchPopup();
        }
    });

    const movieModal = document.getElementById('movieModal');
    const closeModalBtn = document.querySelector('.close-modal');
    
    function closeMovieModal() {
        if (movieModal) {
            movieModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeMovieModal);
    }
    
    if (movieModal) {
        movieModal.addEventListener('click', (e) => {
            if (e.target === movieModal) {
                closeMovieModal();
            }
        });
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && movieModal && movieModal.style.display === 'flex') {
            closeMovieModal();
        }
    });
    
    // Modal loved button handler
    const modalLovedBtn = document.getElementById('modalLovedBtn');
    if (modalLovedBtn) {
        modalLovedBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const title = this.getAttribute('data-title');
            const link = this.getAttribute('data-link');
            const image = this.getAttribute('data-image');
            const language = this.getAttribute('data-language');
            const genre = this.getAttribute('data-genre') || '';
            const website = this.getAttribute('data-website') || '';
            
            if (!title || !link) {
                console.warn('Modal loved button: Missing required movie data');
                return;
            }
            
            let lovedMoviesData = [];
            try {
                lovedMoviesData = JSON.parse(localStorage.getItem('lovedMoviesData') || '[]');
            } catch (error) {
                console.warn('Could not read loved movies data');
            }
            
            const isLoved = lovedMoviesData.some(movie => movie.title === title);
            
            if (!isLoved) {
                // Add to loved
                const movieData = {
                    title: title,
                    link: link,
                    image: image,
                    language: language,
                    genre: genre,
                    website: website,
                    lovedAt: new Date().toISOString()
                };
                lovedMoviesData.unshift(movieData);
                
                try {
                    localStorage.setItem('lovedMoviesData', JSON.stringify(lovedMoviesData));
                    this.classList.add('loved');
                    this.setAttribute('title', 'Remove from Loved');
                    
                    // Dispatch custom event for loved movies update
                    window.dispatchEvent(new CustomEvent('lovedMoviesUpdated', { 
                        detail: { action: 'added', movie: movieData } 
                    }));
                } catch (error) {
                    console.warn('Could not save loved movie to storage');
                }
            } else {
                // Remove from loved
                lovedMoviesData = lovedMoviesData.filter(movie => movie.title !== title);
                
                try {
                    localStorage.setItem('lovedMoviesData', JSON.stringify(lovedMoviesData));
                    this.classList.remove('loved');
                    this.setAttribute('title', 'Add to Loved');
                    
                    // Dispatch custom event for loved movies update
                    window.dispatchEvent(new CustomEvent('lovedMoviesUpdated', { 
                        detail: { action: 'removed', movieTitle: title } 
                    }));
                } catch (error) {
                    console.warn('Could not remove loved movie from storage');
                }
            }
        });
    }

    let currentActiveCategory = 'all';
    let actionPage = 1;
    let animationPage = 1;
    let comedyPage = 1;
    let romancePage = 1;
    let crimePage = 1;
    let fantasyPage = 1;
    let horrorPage = 1;
    let scifiPage = 1;
    let thrillerPage = 1;
    let dramaPage = 1;
    let familyPage = 1;
    let adventurePage = 1;
    let biographyPage = 1;
    let warPage = 1;
    let documentaryPage = 1;

    // Dynamic placeholder rotation with trending movies
    let moviePlaceholders = [];
    let currentPlaceholderIndex = 0;
    let placeholderInterval = null;
    
    // Navbar placeholder
    let navPlaceholderIndex = 0;
    let navPlaceholderInterval = null;

    function rotatePlaceholder() {
        if (moviePlaceholders.length > 0) {
            const newText = moviePlaceholders[currentPlaceholderIndex];
            
            // Add slide-out animation
            placeholderText.classList.add('slide-out');
            
            // After animation, change text and slide in
            setTimeout(() => {
                placeholderText.textContent = newText;
                placeholderText.classList.remove('slide-out');
                placeholderText.classList.add('slide-in');
                
                // Remove slide-in class after animation
                setTimeout(() => {
                    placeholderText.classList.remove('slide-in');
                }, 600);
            }, 600);
            
            currentPlaceholderIndex = (currentPlaceholderIndex + 1) % moviePlaceholders.length;
        }
    }
    
    function rotateNavPlaceholder() {
        if (moviePlaceholders.length > 0 && navPlaceholderText) {
            const newText = moviePlaceholders[navPlaceholderIndex];
            
            navPlaceholderText.classList.add('slide-out');
            
            setTimeout(() => {
                navPlaceholderText.textContent = newText || 'Search Movies & Series';
                navPlaceholderText.classList.remove('slide-out');
                navPlaceholderText.classList.add('slide-in');
                
                setTimeout(() => {
                    navPlaceholderText.classList.remove('slide-in');
                }, 600);
            }, 600);
            
            navPlaceholderIndex = (navPlaceholderIndex + 1) % moviePlaceholders.length;
        } else if (navPlaceholderText) {
            navPlaceholderText.textContent = 'Search Movies & Series';
        }
    }

    function cleanMovieTitle(title) {
        if (!title) return title;
        
        let cleaned = title;
        
        // Remove platform names and abbreviations
        cleaned = cleaned.replace(/\b(Netflix|Amazon|Prime|Disney|Hotstar|ZEE5|SonyLIV|NETFLiX|Original|AMZN|NFLX)\b[-\s]*/gi, '');
        
        // Remove content within various brackets FIRST
        cleaned = cleaned.replace(/\[.*?\]/g, ''); // Remove all square brackets and content
        cleaned = cleaned.replace(/\((?!\d{4}\)).*?\)/g, ''); // Remove parentheses except (year)
        cleaned = cleaned.replace(/\{.*?\}/g, ''); // Remove curly braces and content
        
        // Remove quotes and their content (more aggressive)
        cleaned = cleaned.replace(/"[^"]*"/g, ''); // Remove quoted text
        cleaned = cleaned.replace(/[""''`]+/g, ''); // Remove all remaining quotes
        
        // Remove quality indicators (all resolutions)
        cleaned = cleaned.replace(/\b\d{3,4}p\b/gi, ''); // 480p, 720p, 1080p, 2160p
        cleaned = cleaned.replace(/\b(WEB-?DL|BluRay|BRRip|HDRip|DVDRip|4K|HD|UHD|CAMRip|DVDScr|HDTS|PreDVD|HDTC|V\d+)\b/gi, '');
        
        // Remove encoding and audio formats
        cleaned = cleaned.replace(/\b(x264|x265|H\.?264|H\.?265|HEVC|10bit|8bit)\b/gi, '');
        cleaned = cleaned.replace(/\b(AAC|DD\d+\.\d+|DDP|Dolby|Atmos|AC3|DTS|FLAC|ORG|ESubs?)\b/gi, '');
        
        // Remove language indicators
        cleaned = cleaned.replace(/\b(Hindi|Tamil|Bengali|Telugu|Malayalam|Kannada|Marathi|Punjabi|English|Dual[- ]?Audio|Multi[- ]?Audio)\b/gi, '');
        
        // Remove season/episode info
        cleaned = cleaned.replace(/Season\s*\d+/gi, '');
        cleaned = cleaned.replace(/\bEpisode\s*\d+/gi, '');
        cleaned = cleaned.replace(/\b[ES]\d+[EP]\d+\b/gi, '');
        cleaned = cleaned.replace(/\b[ES]\d+\b/gi, '');
        
        // Remove common extra words
        cleaned = cleaned.replace(/\b(Added|New|Complete|Full|Proper|REPACK|EXTENDED|Watch|Online|Download|And|Movie|Series|NF|Live|Match|Today)\b/gi, '');
        
        // Remove file size indicators
        cleaned = cleaned.replace(/\b\d+(\.\d+)?\s?(MB|GB)\b/gi, '');
        
        // Remove file extensions and release group tags
        cleaned = cleaned.replace(/\b(mkv|mp4|avi)\b/gi, '');
        cleaned = cleaned.replace(/-[A-Z0-9]+$/gi, ''); // Remove trailing release group
        
        // Clean up ampersands, pipes, dashes, and other symbols (more aggressive)
        cleaned = cleaned.replace(/\s*[&|+]+\s*/g, ' '); // Replace &, |, + with space
        cleaned = cleaned.replace(/\s*[-–—]+\s*$/g, ''); // Remove trailing dashes
        cleaned = cleaned.replace(/^[-–—]+\s*/g, ''); // Remove leading dashes
        
        // Remove trailing colons and other punctuation
        cleaned = cleaned.replace(/\s*[:;,]+\s*$/g, '');
        
        // Clean up multiple spaces and trim
        cleaned = cleaned.replace(/\s+/g, ' ');
        cleaned = cleaned.trim();
        
        return cleaned;
    }

    function startPlaceholderRotation() {
        if (placeholderInterval) {
            clearInterval(placeholderInterval);
        }
        // Set initial text without animation
        if (moviePlaceholders.length > 0) {
            placeholderText.textContent = moviePlaceholders[0];
            currentPlaceholderIndex = 1;
            placeholderInterval = setInterval(rotatePlaceholder, 5000);
        } else {
            // Show default placeholder until trending movies load
            placeholderText.textContent = 'Search Movies & Series';
        }
        
        // Also start navbar placeholder rotation
        if (navPlaceholderInterval) {
            clearInterval(navPlaceholderInterval);
        }
        if (moviePlaceholders.length > 0 && navPlaceholderText) {
            navPlaceholderText.textContent = moviePlaceholders[0];
            navPlaceholderIndex = 1;
            navPlaceholderInterval = setInterval(rotateNavPlaceholder, 5000);
        } else if (navPlaceholderText) {
            navPlaceholderText.textContent = 'Search Movies & Series';
        }
    }

    // Hide placeholder when typing
    searchInput.addEventListener('input', function() {
        if (searchInput.value.trim().length > 0) {
            clearBtn.style.display = 'flex';
            animatedPlaceholder.style.opacity = '0';
            animatedPlaceholder.style.visibility = 'hidden';
        } else {
            clearBtn.style.display = 'none';
            animatedPlaceholder.style.opacity = '1';
            animatedPlaceholder.style.visibility = 'visible';
        }
    });
    
    // Hide navbar placeholder when typing
    if (navSearchInput && navAnimatedPlaceholder) {
        navSearchInput.addEventListener('input', function() {
            if (navSearchInput.value.trim().length > 0) {
                navAnimatedPlaceholder.style.opacity = '0';
                navAnimatedPlaceholder.style.visibility = 'hidden';
            } else {
                navAnimatedPlaceholder.style.opacity = '1';
                navAnimatedPlaceholder.style.visibility = 'visible';
            }
        });
    }

    searchInput.addEventListener('focus', function() {
        if (searchInput.value.trim().length === 0) {
            animatedPlaceholder.style.opacity = '0.3';
        }
    });

    searchInput.addEventListener('blur', function() {
        if (searchInput.value.trim().length === 0) {
            animatedPlaceholder.style.opacity = '1';
        }
    });

    // Initialize with default placeholder
    startPlaceholderRotation();

    // Hero Carousel Variables
    let heroCarouselMovies = [];
    let currentHeroSlide = 0;
    let heroCarouselInterval = null;

    // Migrate legacy favouriteMovies to new lovedMoviesData format
    function migrateLegacyFavourites() {
        try {
            const lovedMoviesData = localStorage.getItem('lovedMoviesData');
            const legacyFavourites = localStorage.getItem('favouriteMovies');
            
            // Only migrate if new format doesn't exist but old format does
            if (!lovedMoviesData && legacyFavourites) {
                const oldFavourites = JSON.parse(legacyFavourites);
                if (Array.isArray(oldFavourites) && oldFavourites.length > 0) {
                    console.log('Migrating legacy favourites to new format...');
                    
                    // Convert old title-only format to new object format with placeholder data
                    const migratedData = oldFavourites.map(title => ({
                        title: title,
                        link: '',
                        image: '',
                        language: '',
                        genre: '',
                        lovedAt: new Date().toISOString()
                    }));
                    
                    // Save to new format
                    localStorage.setItem('lovedMoviesData', JSON.stringify(migratedData));
                    
                    // Remove old format only after successful migration
                    localStorage.removeItem('favouriteMovies');
                    console.log(`Migrated ${migratedData.length} legacy favourites to new format.`);
                }
            }
        } catch (error) {
            console.warn('Failed to migrate legacy favourites:', error);
        }
    }

    // Run migration on load
    migrateLegacyFavourites();

    // Load and display hero carousel
    async function loadHeroCarousel() {
        const heroSection = document.getElementById('heroCarouselSection');
        const skeleton = document.getElementById('heroCarouselSkeleton');
        const container = document.querySelector('.hero-carousel-container');
        const illustrationSection = document.getElementById('heroIllustrationSection');
        
        try {
            const response = await fetch('hero-carousel.php');
            const data = await response.json();
            
            if (data.success && data.count > 0) {
                // Get random 10 movies for hero carousel
                heroCarouselMovies = data.results.slice(0, 10);
                
                // Populate placeholder array with cleaned movie titles from hero carousel
                moviePlaceholders = heroCarouselMovies.map(movie => cleanMovieTitle(movie.title)).filter(title => title && title.length > 0);
                
                // Add default placeholder to the rotation
                if (moviePlaceholders.length > 0) {
                    moviePlaceholders.unshift('Search Movies & Series');
                }
                
                // Restart placeholder rotation with new movie titles
                startPlaceholderRotation();
                
                displayHeroCarousel();
                startHeroCarouselAutoPlay();
                
                // Wait for first image to load before hiding skeleton
                const firstSlide = container?.querySelector('.hero-slide');
                if (firstSlide) {
                    const firstImage = firstSlide.querySelector('img');
                    if (firstImage) {
                        const hideSkeletonOnLoad = () => {
                            if (skeleton) skeleton.style.display = 'none';
                            if (container) container.style.display = 'block';
                            if (illustrationSection) illustrationSection.style.display = 'none';
                        };
                        
                        if (firstImage.complete) {
                            hideSkeletonOnLoad();
                        } else {
                            firstImage.addEventListener('load', hideSkeletonOnLoad, { once: true });
                            firstImage.addEventListener('error', hideSkeletonOnLoad, { once: true });
                        }
                    } else {
                        // Fallback if no image found
                        if (skeleton) skeleton.style.display = 'none';
                        if (container) container.style.display = 'block';
                        if (illustrationSection) illustrationSection.style.display = 'none';
                    }
                } else {
                    // Fallback if no slide found
                    if (skeleton) skeleton.style.display = 'none';
                    if (container) container.style.display = 'block';
                    if (illustrationSection) illustrationSection.style.display = 'none';
                }
            } else {
                // Show illustration section if no data available
                if (heroSection) heroSection.style.display = 'none';
                if (illustrationSection) illustrationSection.style.display = 'block';
                console.warn('No trending movies available for hero carousel, showing illustration');
            }
        } catch (error) {
            console.error('Error loading hero carousel:', error);
            // Show illustration section on error
            if (heroSection) heroSection.style.display = 'none';
            if (illustrationSection) illustrationSection.style.display = 'block';
        }
    }

    // Extract color from top area of image
    function extractDominantColor(imageUrl) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'Anonymous';
            
            img.onload = function() {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    
                    // Sample only from top 30% of the image
                    const topHeight = Math.floor(canvas.height * 0.3);
                    const imageData = ctx.getImageData(0, 0, canvas.width, topHeight);
                    const data = imageData.data;
                    
                    let r = 0, g = 0, b = 0;
                    let count = 0;
                    
                    // Sample pixels from the top area (every 10th pixel for performance)
                    for (let i = 0; i < data.length; i += 40) {
                        r += data[i];
                        g += data[i + 1];
                        b += data[i + 2];
                        count++;
                    }
                    
                    r = Math.floor(r / count);
                    g = Math.floor(g / count);
                    b = Math.floor(b / count);
                    
                    // Make color 50% darker/deeper
                    r = Math.floor(r * 0.5);
                    g = Math.floor(g * 0.5);
                    b = Math.floor(b * 0.5);
                    
                    const color = { r, g, b };
                    
                    resolve(color);
                } catch (error) {
                    reject(error);
                }
            };
            
            img.onerror = () => reject(new Error('Failed to load image'));
            // Use image proxy to bypass CORS
            img.src = `image-proxy.php?url=${encodeURIComponent(imageUrl)}`;
        });
    }

    function createSlide(movie) {
        const slide = document.createElement('div');
        slide.className = 'hero-carousel-slide';
        slide.style.backgroundImage = `url('${movie.image}')`;
        
        slide.innerHTML = `
            <div class="hero-carousel-content">
                <div class="hero-carousel-buttons">
                    <button class="hero-carousel-info-btn" title="More Info" data-title="${cleanMovieTitle(movie.title)}" data-link="${movie.link}" data-image="${movie.image}" data-language="${movie.language || ''}" data-genre="${movie.genre || ''}" data-website="${movie.website || ''}">
                        <svg viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M10 10V14M10 6V7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <button class="hero-carousel-play-btn" data-title="${cleanMovieTitle(movie.title)}" data-link="${movie.link}" data-image="${movie.image}" data-language="${movie.language || ''}" data-genre="${movie.genre || ''}" data-website="${movie.website || ''}">
                        <svg viewBox="0 0 24 24" fill="currentColor" class="play-btn-icon">
                            <path d="M8 6.82v10.36c0 .79.87 1.27 1.54.84l8.14-5.18a1 1 0 0 0 0-1.69L9.54 5.98A1 1 0 0 0 8 6.82z"/>
                        </svg>
                        Play
                    </button>
                    <button class="hero-carousel-add-btn" title="Add to Favourites" data-title="${cleanMovieTitle(movie.title)}" data-link="${movie.link}" data-image="${movie.image}" data-language="${movie.language || ''}" data-genre="${movie.genre || ''}" data-website="${movie.website || ''}">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path class="heart-path" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        // Preload image for blur effect
        const img = new Image();
        img.onload = function() {
            slide.classList.add('loaded');
        };
        img.onerror = function() {
            slide.classList.add('loaded');
        };
        img.src = movie.image;
        
        // Extract dominant color and apply to top gradient only
        extractDominantColor(movie.image).then(color => {
            slide.style.setProperty('--gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
        }).catch(error => {
            console.warn('Failed to extract color, using default black:', error);
        });
        
        return slide;
    }

    function displayHeroCarousel() {
        const track = document.getElementById('heroCarouselTrack');
        const dotsContainer = document.getElementById('heroCarouselDots');
        
        if (!track || !dotsContainer) return;
        
        // Create global gradient for heart icons if not exists
        if (!document.getElementById('globalHeartGradient')) {
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', '0');
            svg.setAttribute('height', '0');
            svg.style.position = 'absolute';
            
            const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
            gradient.setAttribute('id', 'globalHeartGradient');
            gradient.setAttribute('x1', '0%');
            gradient.setAttribute('y1', '0%');
            gradient.setAttribute('x2', '100%');
            gradient.setAttribute('y2', '0%');
            
            const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stop1.setAttribute('offset', '0%');
            stop1.setAttribute('style', 'stop-color:#df0033;stop-opacity:1');
            
            const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stop2.setAttribute('offset', '100%');
            stop2.setAttribute('style', 'stop-color:#bd284b;stop-opacity:1');
            
            gradient.appendChild(stop1);
            gradient.appendChild(stop2);
            defs.appendChild(gradient);
            svg.appendChild(defs);
            document.body.appendChild(svg);
        }
        
        track.innerHTML = '';
        dotsContainer.innerHTML = '';
        
        // Clone last slide and add at beginning for seamless loop
        const lastMovie = heroCarouselMovies[heroCarouselMovies.length - 1];
        const lastSlideClone = createSlide(lastMovie);
        lastSlideClone.setAttribute('data-clone', 'last');
        track.appendChild(lastSlideClone);
        
        // Add all actual slides
        heroCarouselMovies.forEach(async (movie, index) => {
            const slide = createSlide(movie);
            track.appendChild(slide);
            
            // Create dot
            const dot = document.createElement('button');
            dot.className = `hero-carousel-dot ${index === 0 ? 'active' : ''}`;
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                goToHeroSlide(index + 1); // +1 because of clone
            });
            dotsContainer.appendChild(dot);
        });
        
        // Clone first slide and add at end for seamless loop
        const firstMovie = heroCarouselMovies[0];
        const firstSlideClone = createSlide(firstMovie);
        firstSlideClone.setAttribute('data-clone', 'first');
        track.appendChild(firstSlideClone);
        
        // Start at actual first slide (index 1, after the cloned last slide)
        currentHeroSlide = 1;
        track.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
        
        // Initialize play button clicks after DOM is fully rendered
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                initializeHeroCarouselPlayButtons();
            });
        });
        
        // Initialize navigation arrows
        const prevBtn = document.getElementById('heroCarouselPrevBtn');
        const nextBtn = document.getElementById('heroCarouselNextBtn');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', prevHeroSlide);
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', nextHeroSlide);
        }
        
        // Update navbar color after a short delay to allow color extraction to complete
        setTimeout(() => {
            updateNavbarColor(true); // Skip transition on initial load
        }, 500);
    }

    function initializeHeroCarouselPlayButtons() {
        const playButtons = document.querySelectorAll('.hero-carousel-play-btn');

        playButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                openMovieModal(button);
            });
        });
        
        // Initialize info button clicks
        const infoButtons = document.querySelectorAll('.hero-carousel-info-btn');
        infoButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                openMovieModal(button);
            });
        });
        
        // Initialize favourite button clicks
        const favouriteButtons = document.querySelectorAll('.hero-carousel-add-btn');
        
        // Load saved favourite movies data from cache
        let lovedMoviesData = [];
        try {
            lovedMoviesData = JSON.parse(localStorage.getItem('lovedMoviesData') || '[]');
        } catch (error) {
            console.warn('localStorage not available, loved movies will not persist');
        }
        
        favouriteButtons.forEach(button => {
            const movieTitle = button.getAttribute('data-title');
            const heartPath = button.querySelector('.heart-path');
            
            // Check if movie is already loved
            const isLoved = lovedMoviesData.some(movie => movie.title === movieTitle);
            if (isLoved) {
                heartPath.setAttribute('fill', 'url(#globalHeartGradient)');
                heartPath.setAttribute('stroke', 'url(#globalHeartGradient)');
            }
            
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const currentFill = heartPath.getAttribute('fill');
                const movieData = {
                    title: button.getAttribute('data-title'),
                    link: button.getAttribute('data-link'),
                    image: button.getAttribute('data-image'),
                    language: button.getAttribute('data-language') || '',
                    genre: button.getAttribute('data-genre') || '',
                    website: button.getAttribute('data-website') || '',
                    lovedAt: new Date().toISOString()
                };
                
                if (currentFill === 'none' || !currentFill) {
                    heartPath.setAttribute('fill', 'url(#globalHeartGradient)');
                    heartPath.setAttribute('stroke', 'url(#globalHeartGradient)');
                    
                    // Trigger animation
                    button.classList.add('hero-carousel-loved');
                    setTimeout(() => {
                        button.classList.remove('hero-carousel-loved');
                    }, 300);
                    
                    // Add to loved movies if not already present
                    const exists = lovedMoviesData.some(movie => movie.title === movieData.title);
                    if (!exists) {
                        lovedMoviesData.unshift(movieData);
                        try {
                            localStorage.setItem('lovedMoviesData', JSON.stringify(lovedMoviesData));
                        } catch (error) {
                            console.warn('Could not save loved movie to cache');
                        }
                    }
                } else {
                    heartPath.setAttribute('fill', 'none');
                    heartPath.setAttribute('stroke', 'currentColor');
                    
                    // Remove from loved movies
                    lovedMoviesData = lovedMoviesData.filter(movie => movie.title !== movieData.title);
                    try {
                        localStorage.setItem('lovedMoviesData', JSON.stringify(lovedMoviesData));
                    } catch (error) {
                        console.warn('Could not remove loved movie from cache');
                    }
                }
            });
        });
    }

    function goToHeroSlide(index) {
        currentHeroSlide = index;
        updateHeroCarousel();
        resetHeroCarouselAutoPlay();
    }

    function prevHeroSlide() {
        currentHeroSlide--;
        updateHeroCarousel();
        
        // If we're at the cloned last slide (index 0)
        if (currentHeroSlide === 0) {
            setTimeout(() => {
                currentHeroSlide = heroCarouselMovies.length; // Jump to actual last slide
                updateHeroCarousel(true); // Skip transition for instant jump
            }, 600); // Wait for transition to complete
        }
        
        resetHeroCarouselAutoPlay();
    }

    function nextHeroSlide() {
        currentHeroSlide++;
        updateHeroCarousel();
        
        // If we're at the cloned first slide (last position)
        if (currentHeroSlide === heroCarouselMovies.length + 1) {
            setTimeout(() => {
                currentHeroSlide = 1; // Jump to actual first slide
                updateHeroCarousel(true); // Skip transition for instant jump
            }, 600); // Wait for transition to complete
        }
        
        resetHeroCarouselAutoPlay();
    }

    function updateHeroCarousel(skipTransition = false) {
        const track = document.getElementById('heroCarouselTrack');
        const dots = document.querySelectorAll('.hero-carousel-dot');
        
        if (!track) return;
        
        // Disable transition for instant jumps
        if (skipTransition) {
            track.style.transition = 'none';
        }
        
        track.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
        
        // Re-enable transition after instant jump
        if (skipTransition) {
            setTimeout(() => {
                track.style.transition = 'transform 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            }, 50);
        }
        
        // Update dots (adjust index because of clone at beginning)
        const actualIndex = currentHeroSlide - 1;
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === actualIndex);
        });
        
        // Update navbar color to match current slide (pass skipTransition)
        updateNavbarColor(skipTransition);
    }
    
    let colorTransitionFrame = null;
    let currentThemeColor = { r: 0, g: 0, b: 0 };
    
    function updateNavbarColor(skipTransition = false) {
        const slides = document.querySelectorAll('.hero-carousel-slide');
        const themeColorMeta = document.getElementById('themeColor');
        
        if (!slides.length || !themeColorMeta) return;
        
        if (searchPopupModal && searchPopupModal.classList.contains('show')) {
            return;
        }
        
        // Don't update meta color if navbar is scrolled (black state)
        if (window.scrollY > 50) {
            return;
        }
        
        // Get the current slide (accounting for clones)
        const currentSlide = slides[currentHeroSlide];
        
        if (currentSlide) {
            // Get the gradient color from the current slide
            const gradientColor = currentSlide.style.getPropertyValue('--gradient-color-top');
            
            if (gradientColor) {
                // Extract RGB values from rgba string
                const rgbaMatch = gradientColor.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
                if (rgbaMatch) {
                    const targetR = parseInt(rgbaMatch[1]);
                    const targetG = parseInt(rgbaMatch[2]);
                    const targetB = parseInt(rgbaMatch[3]);
                    
                    // If skip transition (for instant jumps), set immediately
                    if (skipTransition) {
                        currentThemeColor = { r: targetR, g: targetG, b: targetB };
                        const hexColor = rgbToHex(targetR, targetG, targetB);
                        themeColorMeta.setAttribute('content', hexColor);
                        return;
                    }
                    
                    // Cancel any ongoing transition
                    if (colorTransitionFrame) {
                        cancelAnimationFrame(colorTransitionFrame);
                    }
                    
                    // Smooth transition from current to target color
                    const startR = currentThemeColor.r;
                    const startG = currentThemeColor.g;
                    const startB = currentThemeColor.b;
                    const startTime = performance.now();
                    const duration = 600; // Match carousel transition duration
                    
                    function animateColor(currentTime) {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        
                        // Easing function (cubic-bezier approximation)
                        const eased = progress < 0.5 
                            ? 2 * progress * progress 
                            : 1 - Math.pow(-2 * progress + 2, 2) / 2;
                        
                        const r = Math.round(startR + (targetR - startR) * eased);
                        const g = Math.round(startG + (targetG - startG) * eased);
                        const b = Math.round(startB + (targetB - startB) * eased);
                        
                        currentThemeColor = { r, g, b };
                        const hexColor = rgbToHex(r, g, b);
                        themeColorMeta.setAttribute('content', hexColor);
                        
                        if (progress < 1) {
                            colorTransitionFrame = requestAnimationFrame(animateColor);
                        } else {
                            colorTransitionFrame = null;
                        }
                    }
                    
                    colorTransitionFrame = requestAnimationFrame(animateColor);
                }
            }
        }
    }
    
    function rgbToHex(r, g, b) {
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    }

    // Track scroll state to trigger carousel color update when scrolling back to top
    let wasScrolled = window.scrollY > 50;
    window.addEventListener('scroll', function() {
        const isScrolled = window.scrollY > 50;
        
        // Pause carousel auto-play when scrolled down
        if (isScrolled && !wasScrolled) {
            clearInterval(heroCarouselInterval);
        }
        
        // If we just scrolled back to top, update to carousel color and resume auto-play
        if (wasScrolled && !isScrolled) {
            updateNavbarColor();
            startHeroCarouselAutoPlay();
        }
        
        wasScrolled = isScrolled;
    });

    function startHeroCarouselAutoPlay() {
        clearInterval(heroCarouselInterval);
        heroCarouselInterval = setInterval(nextHeroSlide, 5000);
    }

    function resetHeroCarouselAutoPlay() {
        clearInterval(heroCarouselInterval);
        startHeroCarouselAutoPlay();
    }

    // Pause auto-play on hover
    const heroCarouselSection = document.getElementById('heroCarouselSection');
    heroCarouselSection?.addEventListener('mouseenter', () => {
        clearInterval(heroCarouselInterval);
    });

    heroCarouselSection?.addEventListener('mouseleave', () => {
        startHeroCarouselAutoPlay();
    });

    // Interactive drag support for mobile and desktop
    let isDragging = false;
    let dragStartX = 0;
    let dragCurrentX = 0;
    let dragStartY = 0;
    let dragOffset = 0;
    let dragStartSlideIndex = 0;
    let animationFrameId = null;
    let hasVerticalScroll = false;
    let dragStartTime = 0;
    let dragVelocity = 0;
    let lastDragX = 0;
    let lastDragTime = 0;
    let cachedTrack = null;
    let lastTransformValue = '';

    function updateDragTransform() {
        if (!isDragging) return;
        
        if (cachedTrack) {
            const baseOffset = -currentHeroSlide * 100;
            const dragPercent = (dragOffset / window.innerWidth) * 100;
            const newTransform = `translateX(${baseOffset + dragPercent}%)`;
            
            // Only update if value changed to avoid unnecessary style recalculation
            if (newTransform !== lastTransformValue) {
                cachedTrack.style.transform = newTransform;
                lastTransformValue = newTransform;
            }
        }
        
        animationFrameId = requestAnimationFrame(updateDragTransform);
    }

    function handleDragStart(e) {
        // Prevent drag on dots, buttons, and interactive elements
        if (e.target.classList.contains('hero-carousel-dot') || 
            e.target.closest('.hero-carousel-dot') ||
            e.target.classList.contains('hero-carousel-play-btn') ||
            e.target.closest('.hero-carousel-play-btn') ||
            e.target.classList.contains('hero-carousel-add-btn') ||
            e.target.closest('.hero-carousel-add-btn') ||
            e.target.classList.contains('hero-carousel-info-btn') ||
            e.target.closest('.hero-carousel-info-btn') ||
            e.target.classList.contains('hero-carousel-btn') ||
            e.target.closest('.hero-carousel-btn')) {
            return;
        }
        
        isDragging = true;
        hasVerticalScroll = false;
        dragStartSlideIndex = currentHeroSlide;
        
        const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        
        dragStartX = clientX;
        dragStartY = clientY;
        dragCurrentX = clientX;
        dragOffset = 0;
        dragVelocity = 0;
        dragStartTime = Date.now();
        lastDragX = clientX;
        lastDragTime = Date.now();
        lastTransformValue = '';
        
        clearInterval(heroCarouselInterval);
        
        // Cache track element to avoid repeated DOM queries
        cachedTrack = document.getElementById('heroCarouselTrack');
        if (cachedTrack) {
            cachedTrack.style.transition = 'none';
            cachedTrack.style.willChange = 'transform';
        }
        
        // Start animation loop
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }
        animationFrameId = requestAnimationFrame(updateDragTransform);
    }

    function handleDragMove(e) {
        if (!isDragging || hasVerticalScroll) return;
        
        const clientX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
        const currentTime = Date.now();
        
        const diffX = Math.abs(clientX - dragStartX);
        const diffY = Math.abs(clientY - dragStartY);
        
        // Detect scroll direction in first movement
        if (diffX < 10 && diffY < 10) return;
        
        // If vertical scroll is dominant, cancel drag
        if (diffY > diffX && diffY > 10) {
            hasVerticalScroll = true;
            handleDragEnd(e);
            return;
        }
        
        // Prevent default to avoid page scroll during horizontal drag
        if (diffX > 10) {
            e.preventDefault();
        }
        
        // Calculate velocity (pixels per millisecond)
        const timeDiff = currentTime - lastDragTime;
        if (timeDiff > 0) {
            const distance = clientX - lastDragX;
            dragVelocity = distance / timeDiff;
        }
        
        lastDragX = clientX;
        lastDragTime = currentTime;
        dragCurrentX = clientX;
        dragOffset = dragCurrentX - dragStartX;
    }

    function handleDragEnd(e) {
        if (!isDragging) return;
        
        isDragging = false;
        
        const track = cachedTrack || document.getElementById('heroCarouselTrack');
        
        // If it was vertical scroll, don't change slide
        if (hasVerticalScroll) {
            // Stop animation loop and smoothly return
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
            }
            
            if (track) {
                track.style.willChange = 'auto';
                track.style.transition = 'transform 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                track.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
            }
            startHeroCarouselAutoPlay();
            dragOffset = 0;
            hasVerticalScroll = false;
            return;
        }
        
        // Calculate velocity-based behavior
        const absVelocity = Math.abs(dragVelocity);
        const threshold = window.innerWidth * 0.5; // 50% of screen width
        
        // Determine if we should change slide based on distance OR velocity
        let shouldChangeSlide = Math.abs(dragOffset) > threshold;
        
        // If velocity is high enough (fast swipe), change slide even with small distance
        if (absVelocity > 0.5) { // 0.5 pixels per millisecond = fast swipe
            shouldChangeSlide = true;
        }
        
        // Determine target slide
        let targetSlide = currentHeroSlide;
        
        if (shouldChangeSlide) {
            if (dragOffset > 0 || dragVelocity > 0) {
                // Dragged/swiped right - go to previous slide
                targetSlide = currentHeroSlide - 1;
            } else {
                // Dragged/swiped left - go to next slide
                targetSlide = currentHeroSlide + 1;
            }
        }
        
        // Calculate transition duration based on velocity
        // Fast swipe = shorter duration, slow drag = longer duration
        let transitionDuration = 0.4; // Default 400ms
        
        if (absVelocity > 1.5) {
            // Very fast swipe
            transitionDuration = 0.2;
        } else if (absVelocity > 0.8) {
            // Fast swipe
            transitionDuration = 0.25;
        } else if (absVelocity > 0.4) {
            // Medium swipe
            transitionDuration = 0.3;
        } else if (absVelocity < 0.1) {
            // Very slow drag
            transitionDuration = 0.5;
        }
        
        // Continue the drag animation one more frame to ensure smooth position
        // Then enable transition and move to target
        if (track && animationFrameId) {
            // Let the last animation frame complete
            requestAnimationFrame(() => {
                // Stop further animation
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
                
                // Now enable transition from current position with velocity-based duration
                track.style.willChange = 'auto';
                
                // Use ease-out for natural deceleration
                const easing = absVelocity > 0.5 ? 'cubic-bezier(0.25, 0.46, 0.45, 0.94)' : 'cubic-bezier(0.4, 0, 0.2, 1)';
                track.style.transition = `transform ${transitionDuration}s ${easing}`;
                
                // Move to target position
                if (targetSlide !== currentHeroSlide) {
                    // Moving to different slide
                    currentHeroSlide = targetSlide;
                    track.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
                    
                    // Update dots
                    const dots = document.querySelectorAll('.hero-carousel-dot');
                    const actualIndex = currentHeroSlide - 1;
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === actualIndex);
                    });
                    
                    // Update navbar color
                    updateNavbarColor();
                    
                    // Handle infinite loop clones (use velocity-based duration)
                    if (currentHeroSlide === heroCarouselMovies.length + 1) {
                        setTimeout(() => {
                            currentHeroSlide = 1;
                            updateHeroCarousel(true);
                        }, transitionDuration * 1000);
                    } else if (currentHeroSlide === 0) {
                        setTimeout(() => {
                            currentHeroSlide = heroCarouselMovies.length;
                            updateHeroCarousel(true);
                        }, transitionDuration * 1000);
                    }
                    
                    resetHeroCarouselAutoPlay();
                } else {
                    // Return to current slide
                    track.style.transform = `translateX(-${currentHeroSlide * 100}%)`;
                    startHeroCarouselAutoPlay();
                }
            });
        }
        
        dragOffset = 0;
        dragVelocity = 0;
        hasVerticalScroll = false;
        cachedTrack = null;
        lastTransformValue = '';
    }

    // Touch events
    heroCarouselSection?.addEventListener('touchstart', handleDragStart, { passive: true });
    heroCarouselSection?.addEventListener('touchmove', handleDragMove, { passive: false });
    heroCarouselSection?.addEventListener('touchend', handleDragEnd);
    heroCarouselSection?.addEventListener('touchcancel', handleDragEnd);

    // Mouse events
    heroCarouselSection?.addEventListener('mousedown', handleDragStart);
    
    document.addEventListener('mousemove', (e) => {
        if (isDragging && heroCarouselSection) {
            handleDragMove(e);
        }
    });
    
    document.addEventListener('mouseup', (e) => {
        if (isDragging) {
            handleDragEnd(e);
        }
    });

    // Keyboard navigation support
    document.addEventListener('keydown', (e) => {
        if (heroCarouselMovies.length === 0) return;
        
        const activeElement = document.activeElement;
        const isInputFocused = activeElement && (
            activeElement.tagName === 'INPUT' || 
            activeElement.tagName === 'TEXTAREA' || 
            activeElement.isContentEditable ||
            e.ctrlKey || 
            e.metaKey || 
            e.shiftKey || 
            e.altKey
        );
        
        if (isInputFocused) return;
        
        if (e.key === 'ArrowLeft') {
            prevHeroSlide();
            resetHeroCarouselAutoPlay();
        } else if (e.key === 'ArrowRight') {
            nextHeroSlide();
            resetHeroCarouselAutoPlay();
        }
    });

    // Lazy load category sections using IntersectionObserver
    function initializeLazyCategorySections() {
        // Build category sections dynamically from CATEGORIES_CONFIG
        const categorySections = [];
        
        if (window.CATEGORIES_CONFIG) {
            window.CATEGORIES_CONFIG.forEach(category => {
                categorySections.push({
                    id: `${category.name}Section`,
                    loader: () => loadCategoryMovies(category.name)
                });
            });
        }

        // Check if IntersectionObserver is supported
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const section = entry.target;
                        if (!section.hasAttribute('data-loaded')) {
                            section.setAttribute('data-loaded', 'true');
                            const sectionData = categorySections.find(s => s.id === section.id);
                            if (sectionData && sectionData.loader) {
                                sectionData.loader();
                                observer.unobserve(section);
                            }
                        }
                    }
                });
            }, {
                root: null,
                rootMargin: '200px',
                threshold: 0.1
            });

            categorySections.forEach(({ id }) => {
                const section = document.getElementById(id);
                if (section) {
                    observer.observe(section);
                }
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            categorySections.forEach(({ loader }) => {
                if (loader) loader();
            });
        }
    }

    // Generic function to display movies for any section
    function displayGenericSectionMovies(sectionKey, movies) {
        const sectionId = sectionKey + 'Section';
        const carouselId = sectionKey + 'Carousel';
        const trackId = sectionKey + '-carousel-track';
        const nextCardId = sectionKey + 'NextCard';
        
        const carouselContainer = document.getElementById(carouselId);
        if (!carouselContainer) {
            console.warn(`Carousel container not found for section: ${sectionKey}`);
            return;
        }
        
        // Store movies data for later use (e.g., next card background)
        genericSectionDataStore[sectionKey] = movies;
        
        // Initialize loaded IDs tracker for this section
        if (!genericSectionLoadedIds[sectionKey]) {
            genericSectionLoadedIds[sectionKey] = new Set();
        }
        
        // Track initial movie IDs to prevent duplicates
        movies.forEach(movie => {
            const movieId = getMovieIdentifier(movie);
            genericSectionLoadedIds[sectionKey].add(movieId);
        });
        
        let html = `
            <div class="carousel-container">
                <button class="carousel-btn carousel-btn-prev" data-carousel="${trackId}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                <div class="carousel-wrapper">
                    <div class="carousel-track" id="${trackId}">
        `;
        
        movies.forEach(movie => {
            html += `
                <div class="carousel-item-wrapper">
                    <div class="carousel-item" data-title="${escapeHtml(movie.title)}" data-link="${escapeHtml(movie.link)}" data-image="${escapeHtml(movie.image || '')}" data-language="${escapeHtml(movie.language || '')}" data-genre="${escapeHtml(movie.genre || '')}" data-imdb="${escapeHtml(movie.imdb || '')}" data-website="${escapeHtml(movie.website || '')}">
                        ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                    </div>
                    <div class="carousel-item-title" title="${escapeHtml(movie.title)}">${escapeHtml(movie.title)}</div>
                </div>
            `;
        });
        
        // Only show Next button if there are enough movies (8 or more suggests more content available)
        console.log(`${sectionKey}: movies.length = ${movies.length}, showing Next card: ${movies.length >= 8}`);
        if (movies.length >= 8) {
            html += `
                        <div class="trending-next-card" id="${nextCardId}">
                            <span class="trending-next-card-text">Next</span>
                        </div>
            `;
        }
        
        html += `
                    </div>
                </div>
                <button class="carousel-btn carousel-btn-next" data-carousel="${trackId}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        `;
        
        carouselContainer.innerHTML = html;
        
        // Initialize carousel controls
        const carousel = document.getElementById(trackId);
        const prevBtn = document.querySelector(`.carousel-btn-prev[data-carousel="${trackId}"]`);
        const nextBtn = document.querySelector(`.carousel-btn-next[data-carousel="${trackId}"]`);
        
        if (carousel && prevBtn && nextBtn) {
            function updateArrowsVisibility() {
                const scrollLeft = carousel.scrollLeft;
                const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                
                if (scrollLeft <= 0) {
                    prevBtn.style.display = 'none';
                } else {
                    prevBtn.style.display = 'flex';
                }
                
                if (scrollLeft >= maxScroll - 1) {
                    nextBtn.style.display = 'none';
                } else {
                    nextBtn.style.display = 'flex';
                }
            }
            
            updateArrowsVisibility();
            carousel.addEventListener('scroll', updateArrowsVisibility);
            
            prevBtn.addEventListener('click', () => {
                const scrollAmount = carousel.offsetWidth * 0.8;
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
            
            nextBtn.addEventListener('click', () => {
                const scrollAmount = carousel.offsetWidth * 0.8;
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }
        
        // Initialize click handlers
        const items = document.querySelectorAll(`#${carouselId} .carousel-item:not([data-click-initialized])`);
        
        items.forEach(item => {
            item.setAttribute('data-click-initialized', 'true');
            item.addEventListener('click', function() {
                openMovieModal(this);
            });
        });
        
        initializeLazyImages();
        
        // Load next card background image
        loadGenericSectionNextCardBackground(sectionKey);
    }

    // Load deduplicated sections - ensures no movie appears in multiple sections
    async function loadDeduplicatedSections(page = 1) {
        try {
            const response = await fetch(`deduplicated-sections.php?page=${page}`);
            const data = await response.json();
            
            if (data.success && data.sections) {
                // Process all sections dynamically using generic display function
                for (const [sectionKey, sectionData] of Object.entries(data.sections)) {
                    const sectionId = sectionKey + 'Section';
                    const section = document.getElementById(sectionId);
                    
                    if (sectionData.count > 0) {
                        // Use generic display function for all sections
                        displayGenericSectionMovies(sectionKey, sectionData.results);
                    } else {
                        // Hide section if no movies
                        if (section) section.style.display = 'none';
                    }
                }
                
                // Display Weekly Top 10
                const weeklyTop10Data = await getWeeklyTop10();
                displayWeeklyTop10(weeklyTop10Data);
            }
        } catch (error) {
            console.error('Error loading deduplicated sections:', error);
        }
    }

    // Initialize with staged loading strategy
    async function initializeContentLoading() {
        try {
            // Stage 1: Load hero carousel and deduplicated sections
            await loadHeroCarousel();
            await loadDeduplicatedSections();
            
            // Stage 2: Lazy load category sections when they become visible
            initializeLazyCategorySections();
            
        } catch (error) {
            console.error('Error during content initialization:', error);
        }
    }
    
    initializeContentLoading();
    initializeCategories();

    document.addEventListener('click', async function(e) {
        // Dynamic category sections
        if (window.CATEGORIES_CONFIG) {
            for (const category of window.CATEGORIES_CONFIG) {
                if (e.target.closest(`#${category.name}NextCard`)) {
                    categoryPages[category.name]++;
                    await loadMoreCategoryMovies(category.name);
                    break;
                }
            }
        }
        
        // Dynamic generic sections from ALL_SECTIONS_CONFIG
        if (window.ALL_SECTIONS_CONFIG) {
            for (const section of window.ALL_SECTIONS_CONFIG) {
                if (e.target.closest(`#${section.name}NextCard`)) {
                    genericSectionPages[section.name]++;
                    await loadMoreGenericSection(section.name);
                    break;
                }
            }
        }
    });


    filterBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isVisible = filterDropdown.style.display === 'block';
        filterDropdown.style.display = isVisible ? 'none' : 'block';
        filterBtn.classList.toggle('active', !isVisible);
    });

    document.addEventListener('click', function(e) {
        if (!filterDropdown.contains(e.target) && e.target !== filterBtn) {
            filterDropdown.style.display = 'none';
            filterBtn.classList.remove('active');
        }
    });

    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = Array.from(filterCheckboxes).filter(cb => cb.checked);
            
            if (checkedBoxes.length === 0) {
                this.checked = true;
                return;
            }
            
            selectedWebsites = checkedBoxes.map(cb => cb.value);
        });
    });

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        clearBtn.style.display = 'none';
        animatedPlaceholder.style.opacity = '1';
        animatedPlaceholder.style.visibility = 'visible';
        searchInput.focus();
        hideResults();
        hideError();
        
        // Show category sections again when clearing search
        const categoriesSection = document.getElementById('categoriesSection');
        if (categoriesSection) {
            categoriesSection.style.display = 'block';
        }
        
        // Reset to 'All' category and show default sections
        const categoryItems = document.querySelectorAll('.category-item');
        categoryItems.forEach(btn => btn.classList.remove('active'));
        const allCategoryBtn = document.querySelector('.category-item[data-category="all"]');
        if (allCategoryBtn) {
            allCategoryBtn.classList.add('active');
        }
        filterByCategory('all');
    });

    // Navbar search functionality
    if (navSearchInput && navSearchBtn) {
        // Trigger search on button click
        navSearchBtn.addEventListener('click', function() {
            const query = navSearchInput.value.trim();
            if (query) {
                searchInput.value = query;
                searchForm.dispatchEvent(new Event('submit'));
            }
        });

        // Trigger search on Enter key
        navSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = navSearchInput.value.trim();
                if (query) {
                    searchInput.value = query;
                    searchForm.dispatchEvent(new Event('submit'));
                }
            }
        });
    }

    searchForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const query = searchInput.value.trim();
        if (!query) {
            showError('Please enter something in the search box');
            return;
        }

        if (selectedWebsites.length === 0) {
            showError('Please select at least one website to search');
            return;
        }

        hideError();
        hideResults();
        disableSearch();
        filterDropdown.style.display = 'none';
        filterBtn.classList.remove('active');

        // Hide all category sections when showing search results
        const categoriesSection = document.getElementById('categoriesSection');
        const actionSection = document.getElementById('actionSection');
        const animationSection = document.getElementById('animationSection');
        const comedySection = document.getElementById('comedySection');
        const romanceSection = document.getElementById('romanceSection');
        const crimeSection = document.getElementById('crimeSection');
        const fantasySection = document.getElementById('fantasySection');
        const horrorSection = document.getElementById('horrorSection');
        const scifiSection = document.getElementById('scifiSection');
        const thrillerSection = document.getElementById('thrillerSection');
        const dramaSection = document.getElementById('dramaSection');
        const familySection = document.getElementById('familySection');
        const adventureSection = document.getElementById('adventureSection');
        const biographySection = document.getElementById('biographySection');
        const warSection = document.getElementById('warSection');
        const documentarySection = document.getElementById('documentarySection');

        if (categoriesSection) categoriesSection.style.display = 'none';
        if (actionSection) actionSection.style.display = 'none';
        if (animationSection) animationSection.style.display = 'none';
        if (comedySection) comedySection.style.display = 'none';
        if (romanceSection) romanceSection.style.display = 'none';
        if (crimeSection) crimeSection.style.display = 'none';
        if (fantasySection) fantasySection.style.display = 'none';
        if (horrorSection) horrorSection.style.display = 'none';
        if (scifiSection) scifiSection.style.display = 'none';
        if (thrillerSection) thrillerSection.style.display = 'none';
        if (dramaSection) dramaSection.style.display = 'none';
        if (familySection) familySection.style.display = 'none';
        if (adventureSection) adventureSection.style.display = 'none';
        if (biographySection) biographySection.style.display = 'none';
        if (warSection) warSection.style.display = 'none';
        if (documentarySection) documentarySection.style.display = 'none';

        const websites = selectedWebsites;
        
        results.innerHTML = '';
        showResults();
        
        websites.forEach(websiteName => {
            const placeholderDiv = document.createElement('div');
            placeholderDiv.className = 'website-results';
            placeholderDiv.id = `website-${websiteName}`;
            
            const skeletonHTML = `
                <div class="website-header">
                    <span class="website-name">${websiteName}</span>
                </div>
                <div class="carousel-container">
                    <div class="carousel-wrapper">
                        <div class="carousel-track skeleton-carousel">
                            ${Array(8).fill(0).map(() => `
                                <div class="carousel-item-wrapper">
                                    <div class="carousel-item skeleton-item">
                                        <div class="skeleton-image"></div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            
            placeholderDiv.innerHTML = skeletonHTML;
            results.appendChild(placeholderDiv);
            
            // Apply simple background color to skeleton cards
            const skeletonCards = placeholderDiv.querySelectorAll('.skeleton-image');
            
            skeletonCards.forEach(card => {
                card.style.background = 'rgba(26, 26, 26)';
            });
        });

        const searchPromises = websites.map(websiteName => 
            searchSingleWebsite(websiteName, query)
        );

        Promise.all(searchPromises).then(() => {
            enableSearch();
        }).catch(() => {
            enableSearch();
        });
    });

    async function searchSingleWebsite(websiteName, query) {
        try {
            const response = await fetch(`search-single.php?query=${encodeURIComponent(query)}&website=${encodeURIComponent(websiteName)}`);
            const data = await response.json();

            updateWebsiteResult(websiteName, data);
        } catch (error) {
            updateWebsiteResult(websiteName, {
                success: false,
                website: websiteName,
                error: 'Connection error'
            });
        }
    }

    function updateWebsiteResult(websiteName, data) {
        const websiteDiv = document.getElementById(`website-${websiteName}`);
        if (!websiteDiv) return;

        if (data.success) {
            const headerHTML = `
                <div class="website-header">
                    <span class="website-name">${escapeHtml(data.website)}</span>
                    <span class="result-count">${data.count} results</span>
                </div>
            `;

            if (data.count === 0) {
                websiteDiv.innerHTML = headerHTML + '<div class="no-results">No results found on this site</div>';
            } else {
                const carouselId = `carousel-${websiteName}-${Math.random().toString(36).substr(2, 9)}`;
                let carouselHTML = `
                    <div class="carousel-container">
                        <button class="carousel-btn carousel-btn-prev" data-carousel="${carouselId}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                            </svg>
                        </button>
                        <div class="carousel-wrapper">
                            <div class="carousel-track" id="${carouselId}">
                `;
                
                data.results.forEach(item => {
                    carouselHTML += `
                        <div class="carousel-item-wrapper">
                            <div class="carousel-item" data-title="${escapeHtml(item.title)}" data-link="${escapeHtml(item.link)}" data-image="${escapeHtml(item.image || '')}" data-language="${escapeHtml(item.language || '')}" data-genre="${escapeHtml(item.genre || '')}" data-website="${escapeHtml(item.website || '')}">
                                ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                            </div>
                            <div class="carousel-item-title" title="${escapeHtml(item.title)}">${escapeHtml(item.title)}</div>
                        </div>
                    `;
                });
                
                carouselHTML += `
                            </div>
                        </div>
                        <button class="carousel-btn carousel-btn-next" data-carousel="${carouselId}">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                            </svg>
                        </button>
                    </div>
                `;
                websiteDiv.innerHTML = headerHTML + carouselHTML;
                
                initializeSingleCarousel(carouselId);
                
                // Initialize movie card clicks after DOM is rendered
                requestAnimationFrame(() => {
                    initializeMovieCardClicks();
                });
                
                initializeLazyImages();
            }
        } else {
            websiteDiv.innerHTML = `
                <div class="website-header">
                    <span class="website-name">${escapeHtml(data.website)}</span>
                </div>
                <div class="website-error">
                    ⚠️ Error: ${escapeHtml(data.error)}
                </div>
            `;
        }
    }

    function initializeSingleCarousel(carouselId) {
        const carousel = document.getElementById(carouselId);
        if (!carousel) return;

        const prevBtn = document.querySelector(`.carousel-btn-prev[data-carousel="${carouselId}"]`);
        const nextBtn = document.querySelector(`.carousel-btn-next[data-carousel="${carouselId}"]`);

        function updateArrowsVisibility() {
            const scrollLeft = carousel.scrollLeft;
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;

            if (scrollLeft <= 0) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'flex';
            }

            if (scrollLeft >= maxScroll - 1) {
                nextBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'flex';
            }
        }

        updateArrowsVisibility();
        carousel.addEventListener('scroll', updateArrowsVisibility);

        prevBtn.addEventListener('click', () => {
            const scrollAmount = carousel.offsetWidth * 0.8;
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            const scrollAmount = carousel.offsetWidth * 0.8;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    function displayResults(data) {
        results.innerHTML = '';
        
        let totalResults = 0;
        data.results.forEach(website => {
            if (website.success) {
                totalResults += website.count;
            }
        });

        if (totalResults === 0) {
            results.innerHTML = '<div class="no-results">No results found. Try searching for something else.</div>';
            showResults();
            return;
        }

        data.results.forEach(website => {
            const websiteDiv = document.createElement('div');
            websiteDiv.className = 'website-results';

            if (website.success) {
                const headerHTML = `
                    <div class="website-header">
                        <span class="website-name">${escapeHtml(website.website)}</span>
                        <span class="result-count">${website.count} results</span>
                    </div>
                `;

                if (website.count === 0) {
                    websiteDiv.innerHTML = headerHTML + '<div class="no-results">No results found on this site</div>';
                } else {
                    const carouselId = `carousel-${Math.random().toString(36).substr(2, 9)}`;
                    let carouselHTML = `
                        <div class="carousel-container">
                            <button class="carousel-btn carousel-btn-prev" data-carousel="${carouselId}">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                                </svg>
                            </button>
                            <div class="carousel-wrapper">
                                <div class="carousel-track" id="${carouselId}">
                    `;
                    
                    website.results.forEach(item => {
                        carouselHTML += `
                            <div class="carousel-item-wrapper">
                                <div class="carousel-item" data-title="${escapeHtml(item.title)}" data-link="${escapeHtml(item.link)}" data-image="${escapeHtml(item.image || '')}" data-language="${escapeHtml(item.language || '')}" data-genre="${escapeHtml(item.genre || '')}" data-imdb="${escapeHtml(item.imdb || '')}">
                                    ${item.image ? `<img src="${escapeHtml(item.image)}" alt="${escapeHtml(item.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                                </div>
                                <div class="carousel-item-title" title="${escapeHtml(item.title)}">${escapeHtml(item.title)}</div>
                            </div>
                        `;
                    });
                    
                    carouselHTML += `
                                </div>
                            </div>
                            <button class="carousel-btn carousel-btn-next" data-carousel="${carouselId}">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                                </svg>
                            </button>
                        </div>
                    `;
                    websiteDiv.innerHTML = headerHTML + carouselHTML;
                }
            } else {
                websiteDiv.innerHTML = `
                    <div class="website-header">
                        <span class="website-name">${escapeHtml(website.website)}</span>
                    </div>
                    <div class="website-error">
                        ⚠️ Error fetching data from this site: ${escapeHtml(website.error)}
                    </div>
                `;
            }

            results.appendChild(websiteDiv);
        });

        showResults();
        initializeCarousels();
        
        // Initialize movie card clicks after DOM is rendered
        requestAnimationFrame(() => {
            initializeMovieCardClicks();
        });
        
        initializeLazyImages();
    }

    function scrollToResults() {
        setTimeout(() => {
            window.scrollBy({ 
                top: 200, 
                behavior: 'smooth' 
            });
        }, 100);
    }

    function initializeCarousels() {
        const carousels = document.querySelectorAll('.carousel-track');

        carousels.forEach(carousel => {
            const carouselId = carousel.id;
            const prevBtn = document.querySelector(`.carousel-btn-prev[data-carousel="${carouselId}"]`);
            const nextBtn = document.querySelector(`.carousel-btn-next[data-carousel="${carouselId}"]`);

            function updateArrowsVisibility() {
                const scrollLeft = carousel.scrollLeft;
                const maxScroll = carousel.scrollWidth - carousel.clientWidth;

                if (scrollLeft <= 0) {
                    prevBtn.style.display = 'none';
                } else {
                    prevBtn.style.display = 'flex';
                }

                if (scrollLeft >= maxScroll - 1) {
                    nextBtn.style.display = 'none';
                } else {
                    nextBtn.style.display = 'flex';
                }
            }

            updateArrowsVisibility();

            carousel.addEventListener('scroll', updateArrowsVisibility);

            prevBtn.addEventListener('click', () => {
                const scrollAmount = carousel.offsetWidth * 0.8;
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                const scrollAmount = carousel.offsetWidth * 0.8;
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        });
    }

    function showLoading() {
        loadingMessage.style.display = 'block';
    }

    function hideLoading() {
        loadingMessage.style.display = 'none';
    }

    function showResults() {
        resultsContainer.style.display = 'block';
        const recentViewedContainer = document.getElementById('recentViewedContainer');
        if (recentViewedContainer) {
            const recentViewedMovies = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
            if (recentViewedMovies.length > 0) {
                recentViewedContainer.style.display = 'block';
            }
        }
    }

    function hideResults() {
        resultsContainer.style.display = 'none';
        const recentViewedContainer = document.getElementById('recentViewedContainer');
        if (recentViewedContainer) {
            const recentViewedMovies = JSON.parse(localStorage.getItem('recentViewedMovies') || '[]');
            if (recentViewedMovies.length > 0) {
                recentViewedContainer.style.display = 'block';
            }
        }
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }

    function hideError() {
        errorMessage.style.display = 'none';
    }

    function disableSearch() {
        searchBtn.disabled = true;
        searchBtn.querySelector('.btn-text').style.display = 'none';
        searchBtn.querySelector('.loader').style.display = 'inline-block';
    }

    function enableSearch() {
        searchBtn.disabled = false;
        searchBtn.querySelector('.btn-text').style.display = 'inline';
        searchBtn.querySelector('.loader').style.display = 'none';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }


    // Helper function to open movie modal with all data
    function openMovieModal(item) {
        const modal = document.getElementById('movieModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalLink = document.getElementById('modalLink');
        const modalLanguage = document.getElementById('modalLanguage');
        const modalWebsite = document.getElementById('modalWebsite');
        const modalLovedBtn = document.getElementById('modalLovedBtn');
        
        const title = item.getAttribute('data-title');
        const link = item.getAttribute('data-link');
        const image = item.getAttribute('data-image');
        const language = item.getAttribute('data-language');
        const genre = item.getAttribute('data-genre') || '';
        const website = item.getAttribute('data-website') || '';
        
        // Track movie view for Weekly Top 10
        trackMovieView(title, link, image, language);
        
        // Save to recent viewed movies (skip UI reload, will show after page refresh)
        saveToRecentViewed(title, link, image, language, genre, website, true);
        
        modalTitle.textContent = title;
        modalLink.href = link;
        modalLanguage.textContent = language || '';
        modalWebsite.textContent = website || '';
        
        // Store current movie data in modal button for loved toggle
        if (modalLovedBtn) {
            modalLovedBtn.setAttribute('data-title', title);
            modalLovedBtn.setAttribute('data-link', link);
            modalLovedBtn.setAttribute('data-image', image);
            modalLovedBtn.setAttribute('data-language', language);
            modalLovedBtn.setAttribute('data-genre', genre);
            modalLovedBtn.setAttribute('data-website', website);
            
            // Check if movie is already loved and update button state
            let lovedMoviesData = [];
            try {
                lovedMoviesData = JSON.parse(localStorage.getItem('lovedMoviesData') || '[]');
            } catch (error) {
                console.warn('Could not read loved movies data');
            }
            
            const isLoved = lovedMoviesData.some(movie => movie.title === title);
            if (isLoved) {
                modalLovedBtn.classList.add('loved');
                modalLovedBtn.setAttribute('title', 'Remove from Loved');
            } else {
                modalLovedBtn.classList.remove('loved');
                modalLovedBtn.setAttribute('title', 'Add to Loved');
            }
        }
        
        if (image) {
            modalImage.classList.remove('loaded');
            modal.querySelector('.modal-content').classList.remove('image-loaded');
            
            modalImage.src = image;
            modalImage.style.display = 'block';
            
            // Extract and apply dynamic colors
            extractDominantColor(image).then(color => {
                modal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-top', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
                modal.querySelector('.modal-content').style.setProperty('--modal-gradient-color-bottom', `rgba(${color.r}, ${color.g}, ${color.b}, 1)`);
            }).catch(error => {
                console.warn('Failed to extract color for modal, using defaults:', error);
            });
            
            if (modalImage.complete && modalImage.naturalHeight !== 0) {
                modalImage.classList.add('loaded');
                modal.querySelector('.modal-content').classList.add('image-loaded');
            } else {
                modalImage.onload = function() {
                    modalImage.classList.add('loaded');
                    modal.querySelector('.modal-content').classList.add('image-loaded');
                };
                modalImage.onerror = function() {
                    modalImage.classList.add('loaded');
                    modal.querySelector('.modal-content').classList.add('image-loaded');
                };
            }
        } else {
            modalImage.style.display = 'none';
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }


    function initializeMovieCardClicks() {
        const carouselItems = document.querySelectorAll('.carousel-item:not([data-click-initialized])');

        carouselItems.forEach(item => {
            item.setAttribute('data-click-initialized', 'true');
            item.addEventListener('click', (e) => {
                openMovieModal(item);
            });
        });
    }

    function showSkeletonLoader(carouselElement) {
        const track = carouselElement.querySelector('.carousel-track');
        if (track) {
            const skeletonHTML = Array(8).fill(0).map(() => `
                <div class="carousel-item-wrapper">
                    <div class="carousel-item skeleton-item">
                        <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                    </div>
                </div>
            `).join('');
            track.innerHTML = skeletonHTML;
        }
    }

    // Generic Section Data Store - for dynamically added sections
    const genericSectionDataStore = {};
    const genericSectionPages = {};
    const genericSectionLoadedIds = {}; // Track loaded movie IDs to prevent duplicates

    // Dynamic Category System - supports any category from config
    const categoryPages = {};
    const categoryDataStore = {};
    
    // Helper function to generate unique movie identifier
    function getMovieIdentifier(movie) {
        const normalizedTitle = movie.title.toLowerCase().trim()
            .replace(/\s+/g, ' ')
            .replace(/\b(hindi|english|tamil|telugu|malayalam|kannada|bengali|punjabi|marathi|gujarati|dual audio|multi audio|720p|1080p|480p|web-dl|bluray|hdrip|webrip)\b/gi, '')
            .replace(/\s+/g, ' ')
            .trim();
        
        const linkPath = movie.link ? new URL(movie.link, window.location.origin).pathname : '';
        
        // Create MD5-like hash from normalized title and link path
        let hash = 0;
        const str = normalizedTitle + '|' + linkPath;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash.toString(36);
    }

    // Initialize category data store and page counters from DOM
    function initializeCategoryStore() {
        const categoryItems = document.querySelectorAll('.category-item[data-category]');
        categoryItems.forEach(item => {
            const categoryName = item.getAttribute('data-category');
            if (categoryName && categoryName !== 'all') {
                if (!categoryDataStore[categoryName]) {
                    categoryDataStore[categoryName] = [];
                }
                if (!categoryPages[categoryName]) {
                    categoryPages[categoryName] = 1;
                }
            }
        });
    }

    // Initialize from CATEGORIES_CONFIG if available (fallback)
    if (window.CATEGORIES_CONFIG) {
        window.CATEGORIES_CONFIG.forEach(category => {
            if (!categoryPages[category.name]) {
                categoryPages[category.name] = 1;
            }
            if (!categoryDataStore[category.name]) {
                categoryDataStore[category.name] = [];
            }
        });
    }

    // Initialize from ALL_SECTIONS_CONFIG if available
    if (window.ALL_SECTIONS_CONFIG) {
        window.ALL_SECTIONS_CONFIG.forEach(section => {
            if (!genericSectionPages[section.name]) {
                genericSectionPages[section.name] = 1;
            }
        });
    }

    // Call initialization
    initializeCategoryStore();

    // Generic function to load category movies
    async function loadCategoryMovies(categoryName) {
        const carousel = document.getElementById(`${categoryName}Carousel`);
        const section = document.getElementById(`${categoryName}Section`);
        
        if (!carousel || !section) {
            console.error(`Category elements not found for: ${categoryName}`);
            return;
        }
        
        try {
            const response = await fetch(`categories/index.php?category=${categoryName}`);
            const data = await response.json();
            
            if (data.success && data.count > 0) {
                // Update the category data store dynamically
                if (!categoryDataStore[categoryName]) {
                    categoryDataStore[categoryName] = [];
                }
                categoryDataStore[categoryName] = data.results;
                
                displayCategoryMovies(categoryName, data.results);
            } else {
                console.warn(`No movies found for category: ${categoryName}`);
                carousel.innerHTML = '<div style="padding: 20px; text-align: center; color: rgba(255,255,255,0.5);">No movies available</div>';
            }
        } catch (error) {
            console.error(`Error loading ${categoryName} movies:`, error);
            carousel.innerHTML = '<div style="padding: 20px; text-align: center; color: rgba(255,255,255,0.5);">Failed to load movies</div>';
        }
    }

    // Generic function to load more category movies
    async function loadMoreCategoryMovies(categoryName) {
        const carouselTrack = document.getElementById(`${categoryName}-carousel-track`);
        if (!carouselTrack) return;
        
        const currentScrollPosition = carouselTrack.scrollLeft;
        const itemWidth = carouselTrack.querySelector('.carousel-item-wrapper')?.offsetWidth || 150;
        const containerWidth = carouselTrack.offsetWidth;
        const itemsVisible = Math.floor(containerWidth / itemWidth);
        const itemsToScroll = Math.max(1, itemsVisible - 1);
        const scrollTarget = currentScrollPosition + (itemWidth * itemsToScroll);
        
        // Remove the next card
        const nextCard = document.getElementById(`${categoryName}NextCard`);
        if (nextCard) {
            nextCard.remove();
        }
        
        // Add skeleton loaders
        const skeletonCount = 8;
        for (let i = 0; i < skeletonCount; i++) {
            const currentCount = categoryDataStore[categoryName] ? categoryDataStore[categoryName].length : 0;
            const movieIndex = String(currentCount + i + 1).padStart(2, '0');
            const skeletonHtml = `
                <div class="category-grid-item skeleton-item loading-skeleton">
                    <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                    <div class="category-movie-info">
                        <div class="category-skeleton-title"></div>
                        <div class="category-skeleton-tags">
                            <div class="category-skeleton-tag"></div>
                            <div class="category-skeleton-tag"></div>
                        </div>
                    </div>
                    <div class="category-movie-index">${movieIndex}</div>
                </div>
            `;
            carouselTrack.insertAdjacentHTML('beforeend', skeletonHtml);
        }
        
        try {
            const response = await fetch(`categories/index.php?category=${categoryName}&page=${categoryPages[categoryName]}`);
            const data = await response.json();
            
            // Always remove skeleton loaders
            const skeletons = carouselTrack.querySelectorAll('.loading-skeleton');
            skeletons.forEach(skeleton => skeleton.remove());
            
            if (data.success && data.count > 0) {
                displayCategoryMovies(categoryName, data.results, true);
                
                setTimeout(() => {
                    carouselTrack.scrollTo({
                        left: scrollTarget,
                        behavior: 'smooth'
                    });
                }, 100);
            } else {
                // Re-add Load More button if needed
                displayCategoryMovies(categoryName, [], true);
            }
        } catch (error) {
            console.error(`Error loading more ${categoryName} movies:`, error);
            
            // Always remove skeleton loaders on error
            const skeletons = carouselTrack.querySelectorAll('.loading-skeleton');
            skeletons.forEach(skeleton => skeleton.remove());
            
            // Re-add Load More button so user can retry
            displayCategoryMovies(categoryName, [], true);
        }
    }

    // Generic function to load more movies for dynamically added sections
    async function loadMoreGenericSection(sectionName) {
        const carouselTrack = document.getElementById(`${sectionName}-carousel-track`);
        if (!carouselTrack) return;
        
        const currentScrollPosition = carouselTrack.scrollLeft;
        const itemWidth = carouselTrack.querySelector('.carousel-item-wrapper')?.offsetWidth || 150;
        const containerWidth = carouselTrack.offsetWidth;
        const itemsVisible = Math.floor(containerWidth / itemWidth);
        const itemsToScroll = Math.max(1, itemsVisible - 1);
        const scrollTarget = currentScrollPosition + (itemWidth * itemsToScroll);
        
        // Remove the next card
        const nextCard = document.getElementById(`${sectionName}NextCard`);
        if (nextCard) {
            nextCard.remove();
        }
        
        // Add skeleton loaders
        const skeletonCount = 8;
        for (let i = 0; i < skeletonCount; i++) {
            const skeletonHtml = `
                <div class="carousel-item-wrapper loading-skeleton">
                    <div class="carousel-item skeleton-item">
                        <div class="skeleton-image" style="background: rgba(26, 26, 26);"></div>
                    </div>
                </div>
            `;
            carouselTrack.insertAdjacentHTML('beforeend', skeletonHtml);
        }
        
        try {
            const response = await fetch(`deduplicated-sections.php?page=${genericSectionPages[sectionName]}&section=${sectionName}`);
            const data = await response.json();
            
            // Always remove skeleton loaders
            const skeletons = carouselTrack.querySelectorAll('.loading-skeleton');
            skeletons.forEach(skeleton => skeleton.remove());
            
            if (data.success && data.sections && data.sections[sectionName]) {
                const sectionData = data.sections[sectionName];
                if (sectionData.count > 0) {
                    // Initialize data store and loaded IDs tracker if not exists
                    if (!genericSectionDataStore[sectionName]) {
                        genericSectionDataStore[sectionName] = [];
                    }
                    if (!genericSectionLoadedIds[sectionName]) {
                        genericSectionLoadedIds[sectionName] = new Set();
                    }
                    
                    // Filter out duplicate movies
                    let newMoviesCount = 0;
                    sectionData.results.forEach(movie => {
                        const movieId = getMovieIdentifier(movie);
                        
                        // Only add if this movie hasn't been loaded before
                        if (!genericSectionLoadedIds[sectionName].has(movieId)) {
                            genericSectionLoadedIds[sectionName].add(movieId);
                            genericSectionDataStore[sectionName].push(movie);
                            newMoviesCount++;
                            
                            const itemHtml = `
                                <div class="carousel-item-wrapper">
                                    <div class="carousel-item new-item" data-title="${escapeHtml(movie.title)}" data-link="${escapeHtml(movie.link)}" data-image="${escapeHtml(movie.image || '')}" data-language="${escapeHtml(movie.language || '')}" data-genre="${escapeHtml(movie.genre || '')}" data-imdb="${escapeHtml(movie.imdb || '')}">
                                        ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                                    </div>
                                    <div class="carousel-item-title" title="${escapeHtml(movie.title)}">${escapeHtml(movie.title)}</div>
                                </div>
                            `;
                            carouselTrack.insertAdjacentHTML('beforeend', itemHtml);
                        }
                    });
                    
                    // Log deduplication stats
                    if (sectionData.count > newMoviesCount) {
                        console.log(`${sectionName}: Filtered ${sectionData.count - newMoviesCount} duplicate movies, added ${newMoviesCount} new movies`);
                    }
                    
                    // Only show Next button if new movies were added and potentially more exist
                    console.log(`${sectionName} loadMore: newMoviesCount=${newMoviesCount}, sectionData.count=${sectionData.count}, showing Next: ${newMoviesCount > 0 && sectionData.count >= 8}`);
                    if (newMoviesCount > 0 && sectionData.count >= 8) {
                        const nextCardHtml = `
                            <div class="trending-next-card" id="${sectionName}NextCard">
                                <span class="trending-next-card-text">Next</span>
                            </div>
                        `;
                        carouselTrack.insertAdjacentHTML('beforeend', nextCardHtml);
                        loadGenericSectionNextCardBackground(sectionName);
                    }
                    // If no more movies, don't show anything - just end the carousel
                    
                    setTimeout(() => {
                        const newItems = carouselTrack.querySelectorAll('.new-item');
                        newItems.forEach(item => item.classList.remove('new-item'));
                    }, 3000);
                    
                    initializeLazyImages();
                    initializeMovieCardClicks();
                    
                    setTimeout(() => {
                        carouselTrack.scrollTo({
                            left: scrollTarget,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
                // If no results, don't show anything - just end the carousel
            }
            // If response fails, don't show anything - just end the carousel
        } catch (error) {
            console.error(`Error loading more ${sectionName} movies:`, error);
            
            // Always remove skeleton loaders on error
            const skeletons = carouselTrack.querySelectorAll('.loading-skeleton');
            skeletons.forEach(skeleton => skeleton.remove());
            
            // Re-add Next button so user can retry
            const nextCardHtml = `
                <div class="trending-next-card" id="${sectionName}NextCard">
                    <span class="trending-next-card-text">Next</span>
                </div>
            `;
            carouselTrack.insertAdjacentHTML('beforeend', nextCardHtml);
            loadGenericSectionNextCardBackground(sectionName);
        }
    }

    // Generic function to display category movies
    function displayCategoryMovies(categoryName, movies, append = false) {
        const gridContainer = document.getElementById(`${categoryName}Carousel`);
        const gridId = `${categoryName}-carousel-track`;
        
        if (append) {
            const grid = document.getElementById(gridId);
            if (grid) {
                const nextCard = document.getElementById(`${categoryName}NextCard`);
                if (nextCard) {
                    nextCard.remove();
                }
                
                const currentCount = categoryDataStore[categoryName] ? categoryDataStore[categoryName].length : 0;
                
                movies.forEach((movie, index) => {
                    // Update the category data store dynamically
                    if (!categoryDataStore[categoryName]) {
                        categoryDataStore[categoryName] = [];
                    }
                    categoryDataStore[categoryName].push(movie);
                    
                    const movieIndex = String(currentCount + index + 1).padStart(2, '0');
                    const genres = parseGenres(movie.genre, movie.language);
                    const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
                    
                    const categoryTag = movie.category ? `<span class="category-genre-tag">${escapeHtml(movie.category)}</span>` : '';
                    const websiteTag = movie.website ? `<span class="category-genre-tag category-website-tag">${escapeHtml(movie.website.toUpperCase())}</span>` : '';
                    
                    const itemHtml = `
                        <div class="category-grid-item new-item" data-title="${escapeHtml(movie.title)}" data-link="${escapeHtml(movie.link)}" data-image="${escapeHtml(movie.image || '')}" data-language="${escapeHtml(movie.language || '')}" data-genre="${escapeHtml(movie.genre || '')}" data-imdb="${escapeHtml(movie.imdb || '')}">
                            ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                            <div class="category-movie-info">
                                <h3 class="category-movie-title">${escapeHtml(movie.title)}</h3>
                                <div class="category-movie-genres">${genreTags}${categoryTag}${websiteTag}</div>
                            </div>
                            <div class="category-movie-index">${movieIndex}</div>
                        </div>
                    `;
                    grid.insertAdjacentHTML('beforeend', itemHtml);
                });
                
                // Only show Load More button if there are potentially more movies (8 or more were returned)
                if (movies.length >= 8) {
                    const nextCardHtml = `
                        <div class="category-next-card" id="${categoryName}NextCard">
                            <span class="category-next-card-text">Load More</span>
                        </div>
                    `;
                    grid.insertAdjacentHTML('beforeend', nextCardHtml);
                    loadCategoryNextCardBackground(categoryName);
                } else if (movies.length > 0) {
                    // Show "No more movies available" message if fewer than 8 movies were returned
                    const noMoreHtml = `
                        <div style="text-align: center; color: rgba(255,255,255,0.5); padding: 40px 20px; grid-column: 1 / -1; font-size: 1.1rem;">
                            No more movies available
                        </div>
                    `;
                    grid.insertAdjacentHTML('beforeend', noMoreHtml);
                }
                
                setTimeout(() => {
                    const newItems = grid.querySelectorAll('.new-item');
                    newItems.forEach(item => item.classList.remove('new-item'));
                }, 3000);
                
                initializeCategoryClicks(categoryName);
                initializeLazyImages();
                return;
            }
        }
        
        let html = `<div class="category-grid-container" id="${gridId}">`;
        
        movies.forEach((movie, index) => {
            const movieIndex = String(index + 1).padStart(2, '0');
            const genres = parseGenres(movie.genre, movie.language);
            const genreTags = genres.map(g => `<span class="category-genre-tag">${escapeHtml(g)}</span>`).join('');
            
            const categoryTag = movie.category ? `<span class="category-genre-tag">${escapeHtml(movie.category)}</span>` : '';
            const websiteTag = movie.website ? `<span class="category-genre-tag category-website-tag">${escapeHtml(movie.website.toUpperCase())}</span>` : '';
            
            html += `
                <div class="category-grid-item" data-title="${escapeHtml(movie.title)}" data-link="${escapeHtml(movie.link)}" data-image="${escapeHtml(movie.image || '')}" data-language="${escapeHtml(movie.language || '')}" data-genre="${escapeHtml(movie.genre || '')}" data-imdb="${escapeHtml(movie.imdb || '')}" data-website="${escapeHtml(movie.website || '')}">
                    ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                    <div class="category-movie-info">
                        <h3 class="category-movie-title">${escapeHtml(movie.title)}</h3>
                        <div class="category-movie-genres">${genreTags}${categoryTag}${websiteTag}</div>
                    </div>
                    <div class="category-movie-index">${movieIndex}</div>
                </div>
            `;
        });
        
        html += `
            <div class="category-next-card" id="${categoryName}NextCard">
                <span class="category-next-card-text">Load More</span>
            </div>
        </div>`;
        
        gridContainer.innerHTML = html;
        
        initializeCategoryClicks(categoryName);
        initializeLazyImages();
        loadCategoryNextCardBackground(categoryName);
    }
    
    // Helper function to parse genres from movie data
    function parseGenres(genreString, languageString) {
        const genres = [];
        
        if (genreString) {
            const genreParts = genreString.split(',').map(g => g.trim()).filter(g => g);
            genres.push(...genreParts.slice(0, 3));
        }
        
        if (languageString && genres.length < 3) {
            const langParts = languageString.split(',').map(l => l.trim()).filter(l => l);
            genres.push(...langParts.slice(0, 3 - genres.length));
        }
        
        if (genres.length === 0) {
            genres.push('Movie');
        }
        
        return genres.slice(0, 3);
    }

    // Generic function to initialize category carousel
    function initializeCategoryCarousel(categoryName) {
        const carousel = document.getElementById(`${categoryName}-carousel-track`);
        const prevBtn = document.querySelector(`.carousel-btn-prev[data-carousel="${categoryName}-carousel-track"]`);
        const nextBtn = document.querySelector(`.carousel-btn-next[data-carousel="${categoryName}-carousel-track"]`);

        if (!carousel || !prevBtn || !nextBtn) return;

        function updateArrowsVisibility() {
            const scrollLeft = carousel.scrollLeft;
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;

            if (scrollLeft <= 0) {
                prevBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'flex';
            }

            if (scrollLeft >= maxScroll - 1) {
                nextBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'flex';
            }
        }

        updateArrowsVisibility();

        carousel.addEventListener('scroll', updateArrowsVisibility);

        prevBtn.addEventListener('click', () => {
            const scrollAmount = carousel.offsetWidth * 0.8;
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            const scrollAmount = carousel.offsetWidth * 0.8;
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }

    // Generic function to initialize category clicks
    function initializeCategoryClicks(categoryName) {
        const items = document.querySelectorAll(`#${categoryName}Carousel .category-grid-item:not([data-click-initialized])`);
        
        items.forEach(item => {
            item.setAttribute('data-click-initialized', 'true');
            item.addEventListener('click', () => {
                openMovieModal(item);
            });
        });
    }

    // Generic function to load next card background for categories
    function loadCategoryNextCardBackground(categoryName) {
        const nextCard = document.getElementById(`${categoryName}NextCard`);
        
        // Get category data from the dynamic store
        const categoryData = categoryDataStore[categoryName] || [];
        
        if (nextCard && categoryData.length > 0) {
            const randomMovie = categoryData[Math.floor(Math.random() * categoryData.length)];
            if (randomMovie && randomMovie.image) {
                nextCard.style.backgroundImage = `url(${randomMovie.image})`;
                nextCard.style.backgroundSize = 'cover';
                nextCard.style.backgroundPosition = 'center';
            }
        }
    }

    // Generic function to load next card background for generic sections
    function loadGenericSectionNextCardBackground(sectionKey) {
        const nextCard = document.getElementById(`${sectionKey}NextCard`);
        
        // Get section data from the generic store
        const sectionData = genericSectionDataStore[sectionKey] || [];
        
        if (nextCard && sectionData.length > 0) {
            const randomMovie = sectionData[Math.floor(Math.random() * sectionData.length)];
            if (randomMovie && randomMovie.image) {
                nextCard.style.backgroundImage = `url(${randomMovie.image})`;
                nextCard.style.backgroundSize = 'cover';
                nextCard.style.backgroundPosition = 'center';
            }
        }
    }

    function displayWeeklyTop10(movies) {
        const weeklyTop10Section = document.getElementById('weekly_top_10Section');
        const weeklyTop10Carousel = document.getElementById('weekly_top_10Carousel');
        const carouselId = 'weekly_top_10-carousel-track';
        
        if (!weeklyTop10Carousel || !weeklyTop10Section) return;
        
        // Update global tracker
        hasWeeklyTop10Movies = movies.length > 0;
        
        if (movies.length === 0) {
            weeklyTop10Section.style.display = 'none';
            return;
        }
        
        // Only show Weekly Top 10 if current category is 'all'
        if (currentActiveCategory === 'all') {
            weeklyTop10Section.style.display = 'block';
        } else {
            weeklyTop10Section.style.display = 'none';
        }
        
        let carouselHTML = `
            <div class="carousel-container">
                <button class="carousel-btn carousel-btn-prev" data-carousel="${carouselId}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                    </svg>
                </button>
                <div class="carousel-wrapper">
                    <div class="carousel-track" id="${carouselId}">
        `;
        
        movies.forEach((movie, index) => {
            const rank = index + 1;
            carouselHTML += `
                <div class="carousel-item-wrapper">
                    <div class="carousel-item" data-title="${escapeHtml(movie.title)}" data-link="${escapeHtml(movie.link)}" data-image="${escapeHtml(movie.image || '')}" data-language="${escapeHtml(movie.language || '')}" data-rank="${rank}">
                        ${movie.image ? `<img src="${escapeHtml(movie.image)}" alt="${escapeHtml(movie.title)}" class="result-image lazy-image" loading="lazy" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22280%22%3E%3Crect fill=%22%23ddd%22 width=%22200%22 height=%22280%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E'">` : '<div class="result-image"></div>'}
                        <div class="rank-number">${rank}</div>
                    </div>
                    <div class="carousel-item-title" title="${escapeHtml(movie.title)}">${escapeHtml(movie.title)}</div>
                </div>
            `;
        });
        
        carouselHTML += `
                    </div>
                </div>
                <button class="carousel-btn carousel-btn-next" data-carousel="${carouselId}">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="white">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                    </svg>
                </button>
            </div>
        `;
        
        weeklyTop10Carousel.innerHTML = carouselHTML;
        
        setTimeout(() => {
            const images = weeklyTop10Carousel.querySelectorAll('.lazy-image');
            
            for (const img of images) {
                if (img.complete && img.naturalHeight !== 0) {
                    img.classList.add('loaded');
                    img.parentElement.classList.add('image-loaded');
                } else {
                    img.addEventListener('load', function() {
                        this.classList.add('loaded');
                        this.parentElement.classList.add('image-loaded');
                    });
                }
            }
            
            const carouselItems = weeklyTop10Carousel.querySelectorAll('.carousel-item');
            carouselItems.forEach(item => {
                item.addEventListener('click', () => openMovieModal(item));
            });
            
            initializeCarousels();
        }, 50);
    }

    function initializeLazyImages() {
        const lazyImages = document.querySelectorAll('.lazy-image');
        
        for (const img of lazyImages) {
            const parentItem = img.closest('.carousel-item') || img.closest('.category-grid-item');
            
            if (img.complete && img.naturalHeight !== 0) {
                img.classList.add('loaded');
                if (parentItem) parentItem.classList.add('image-loaded');
            } else {
                img.addEventListener('load', function() {
                    this.classList.add('loaded');
                    const parent = this.closest('.carousel-item') || this.closest('.category-grid-item');
                    if (parent) parent.classList.add('image-loaded');
                });
                
                img.addEventListener('error', function() {
                    this.classList.add('loaded');
                    const parent = this.closest('.carousel-item') || this.closest('.category-grid-item');
                    if (parent) parent.classList.add('image-loaded');
                });
            }
        }
    }

    async function loadCategoryPosters() {
        try {
            const response = await fetch('category-posters.php');
            const data = await response.json();
            
            if (data.success && data.posters) {
                const categoryItems = document.querySelectorAll('.category-item');
                categoryItems.forEach(item => {
                    const category = item.getAttribute('data-category');
                    const poster = data.posters[category];
                    
                    if (poster) {
                        item.style.backgroundImage = `url(${poster})`;
                        item.style.backgroundSize = 'cover';
                        item.style.backgroundPosition = 'center';
                    }
                });
            }
        } catch (error) {
            console.error('Error loading category posters:', error);
        }
    }

    function initializeCategories() {
        const categoriesSection = document.getElementById('categoriesSection');
        const categoryItems = document.querySelectorAll('.category-item');
        const categoriesTrack = document.getElementById('categoriesTrack');
        const prevBtn = document.querySelector('.carousel-btn-prev[data-carousel="categoriesTrack"]');
        const nextBtn = document.querySelector('.carousel-btn-next[data-carousel="categoriesTrack"]');
        
        loadCategoryPosters();

        categoryItems.forEach(item => {
            item.addEventListener('click', () => {
                categoryItems.forEach(btn => btn.classList.remove('active'));
                item.classList.add('active');

                const category = item.getAttribute('data-category');
                filterByCategory(category);
            });
        });

        if (prevBtn && nextBtn && categoriesTrack) {
            function updateArrowsVisibility() {
                const scrollLeft = categoriesTrack.scrollLeft;
                const maxScroll = categoriesTrack.scrollWidth - categoriesTrack.clientWidth;

                if (scrollLeft <= 0) {
                    prevBtn.style.opacity = '0';
                    prevBtn.style.pointerEvents = 'none';
                } else {
                    prevBtn.style.opacity = '';
                    prevBtn.style.pointerEvents = 'auto';
                }

                if (scrollLeft >= maxScroll - 1) {
                    nextBtn.style.opacity = '0';
                    nextBtn.style.pointerEvents = 'none';
                } else {
                    nextBtn.style.opacity = '';
                    nextBtn.style.pointerEvents = 'auto';
                }
            }

            prevBtn.addEventListener('click', () => {
                categoriesTrack.scrollBy({
                    left: -400,
                    behavior: 'smooth'
                });
            });

            nextBtn.addEventListener('click', () => {
                categoriesTrack.scrollBy({
                    left: 400,
                    behavior: 'smooth'
                });
            });

            categoriesTrack.addEventListener('scroll', updateArrowsVisibility);
            updateArrowsVisibility();
        }
    }

    function filterByCategory(category) {
        currentActiveCategory = category;
        
        const defaultSections = [];
        if (window.ALL_SECTIONS_CONFIG) {
            window.ALL_SECTIONS_CONFIG.forEach(section => {
                if (section.id) {
                    defaultSections.push(section.id);
                }
            });
        }
        
        if (category === 'all') {
            defaultSections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section) {
                    // Special handling for Weekly Top 10 - only show if it has movies
                    if (sectionId === 'weekly_top_10Section') {
                        if (hasWeeklyTop10Movies) {
                            section.style.display = 'block';
                        } else {
                            section.style.display = 'none';
                        }
                    } else {
                        section.style.display = 'block';
                    }
                }
            });
            
            if (window.CATEGORIES_CONFIG) {
                window.CATEGORIES_CONFIG.forEach(cat => {
                    const section = document.getElementById(`${cat.name}Section`);
                    if (section) section.style.display = 'none';
                });
            }
        } else {
            defaultSections.forEach(sectionId => {
                const section = document.getElementById(sectionId);
                if (section) section.style.display = 'none';
            });
            
            if (window.CATEGORIES_CONFIG) {
                window.CATEGORIES_CONFIG.forEach(cat => {
                    const section = document.getElementById(`${cat.name}Section`);
                    if (section) {
                        if (cat.name === category) {
                            section.style.display = 'block';
                            
                            if (!section.hasAttribute('data-loaded')) {
                                section.setAttribute('data-loaded', 'true');
                                loadCategoryMovies(cat.name);
                            }
                        } else {
                            section.style.display = 'none';
                        }
                    }
                });
            }
        }
    }

    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            faqItems.forEach(otherItem => {
                otherItem.classList.remove('active');
            });
            
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
    const bottomNavSearch = document.getElementById('bottomNavSearch');
    
    function updateBottomNavActive() {
        const currentPage = window.location.pathname;
        
        bottomNavItems.forEach(item => {
            item.classList.remove('active');
        });
        
        if (currentPage.includes('latest.php')) {
            bottomNavItems.forEach(item => {
                if (item.getAttribute('data-page') === 'latest') {
                    item.classList.add('active');
                }
            });
        } else if (currentPage.includes('loved.php')) {
            bottomNavItems.forEach(item => {
                if (item.getAttribute('data-page') === 'loved') {
                    item.classList.add('active');
                }
            });
        } else if (currentPage.includes('about.php')) {
            bottomNavItems.forEach(item => {
                if (item.getAttribute('data-page') === 'about') {
                    item.classList.add('active');
                }
            });
        } else if (currentPage.includes('index.php') || currentPage === '/' || currentPage === '') {
            bottomNavItems.forEach(item => {
                if (item.getAttribute('data-page') === 'home') {
                    item.classList.add('active');
                }
            });
        }
    }
    
    updateBottomNavActive();
    
    // Navigation stack management for smart back/forward behavior
    function getPageKey(url) {
        const urlObj = new URL(url, window.location.origin);
        return urlObj.pathname + urlObj.search;
    }
    
    function getNavStack() {
        const stackData = sessionStorage.getItem('navStack');
        return stackData ? JSON.parse(stackData) : [];
    }
    
    function setNavStack(stack) {
        sessionStorage.setItem('navStack', JSON.stringify(stack));
    }
    
    function getCurrentNavIndex() {
        const index = sessionStorage.getItem('navIndex');
        return index !== null ? parseInt(index) : -1;
    }
    
    function setCurrentNavIndex(index) {
        sessionStorage.setItem('navIndex', index.toString());
    }
    
    // Initialize navigation stack on page load
    let navStack = getNavStack();
    let navIndex = getCurrentNavIndex();
    const currentPageKey = getPageKey(window.location.href);
    
    // Update navigation stack on pageshow (handles both normal loads and bfcache restores)
    window.addEventListener('pageshow', function(event) {
        navStack = getNavStack();
        navIndex = getCurrentNavIndex();
        const pageKey = getPageKey(window.location.href);
        
        if (event.persisted) {
            // Page restored from bfcache
            const existingIndex = navStack.indexOf(pageKey);
            if (existingIndex !== -1) {
                navIndex = existingIndex;
                setCurrentNavIndex(navIndex);
            }
        } else {
            // Normal page load
            if (navIndex === -1 || navStack.length === 0) {
                // First page
                navStack = [pageKey];
                navIndex = 0;
            } else {
                const existingIndex = navStack.indexOf(pageKey);
                if (existingIndex !== -1 && existingIndex === navIndex) {
                    // Same page, no change needed
                } else if (existingIndex !== -1 && existingIndex < navIndex) {
                    // Went back in history
                    navIndex = existingIndex;
                } else {
                    // New page or forward navigation
                    // Trim any forward entries and add new page
                    navStack = navStack.slice(0, navIndex + 1);
                    navStack.push(pageKey);
                    navIndex = navStack.length - 1;
                }
            }
            setNavStack(navStack);
            setCurrentNavIndex(navIndex);
        }
    });
    
    // Handle bottom navigation clicks
    bottomNavItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('data-page') === 'search') {
                return;
            }
            
            const targetHref = this.getAttribute('href');
            if (!targetHref || targetHref.startsWith('#')) {
                bottomNavItems.forEach(navItem => {
                    navItem.classList.remove('active');
                });
                this.classList.add('active');
                return;
            }
            
            // Prevent default navigation
            e.preventDefault();
            
            // Get target page key
            const targetUrl = new URL(targetHref, window.location.origin).href;
            const targetPageKey = getPageKey(targetUrl);
            const currentPageKey = getPageKey(window.location.href);
            
            // Check if already on this page
            if (targetPageKey === currentPageKey) {
                return;
            }
            
            // Check if target exists in navigation stack
            navStack = getNavStack();
            navIndex = getCurrentNavIndex();
            const targetIndex = navStack.indexOf(targetPageKey);
            
            if (targetIndex !== -1 && targetIndex !== navIndex) {
                // Page exists in history - use browser back/forward
                const stepsToGo = targetIndex - navIndex;
                navIndex = targetIndex;
                setCurrentNavIndex(navIndex);
                window.history.go(stepsToGo);
            } else {
                // New page - navigate normally
                window.location.href = targetHref;
            }
            
            bottomNavItems.forEach(navItem => {
                navItem.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    if (bottomNavSearch) {
        bottomNavSearch.addEventListener('click', function() {
            const currentActive = document.querySelector('.bottom-nav-item.active');
            if (currentActive && currentActive !== this) {
                previousActiveNavItem = currentActive;
            }
            
            openSearchPopup();
            
            bottomNavItems.forEach(navItem => {
                navItem.classList.remove('active');
            });
            this.classList.add('active');
        });
    }
    
    window.addEventListener('pageshow', function(event) {
        updateBottomNavActive();
        updateNavLinksActive();
    });

    const navLinks = document.querySelectorAll('.nav-link');
    
    function updateNavLinksActive() {
        const currentPage = window.location.pathname;
        
        navLinks.forEach(link => {
            link.classList.remove('active');
        });
        
        if (currentPage.includes('latest.php')) {
            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === 'latest') {
                    link.classList.add('active');
                }
            });
        } else if (currentPage.includes('loved.php')) {
            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === 'loved') {
                    link.classList.add('active');
                }
            });
        } else if (currentPage.includes('about.php')) {
            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === 'about') {
                    link.classList.add('active');
                }
            });
        } else if (currentPage.includes('index.php') || currentPage === '/' || currentPage === '') {
            navLinks.forEach(link => {
                if (link.getAttribute('data-page') === 'home') {
                    link.classList.add('active');
                }
            });
        }
    }
    
    updateNavLinksActive();
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetHref = this.getAttribute('href');
            if (!targetHref || targetHref.startsWith('#')) {
                navLinks.forEach(navLink => {
                    navLink.classList.remove('active');
                });
                this.classList.add('active');
                return;
            }
            
            // Prevent default navigation
            e.preventDefault();
            
            // Get target page key
            const targetUrl = new URL(targetHref, window.location.origin).href;
            const targetPageKey = getPageKey(targetUrl);
            const currentPageKey = getPageKey(window.location.href);
            
            // Check if already on this page
            if (targetPageKey === currentPageKey) {
                return;
            }
            
            // Check if target exists in navigation stack
            navStack = getNavStack();
            navIndex = getCurrentNavIndex();
            const targetIndex = navStack.indexOf(targetPageKey);
            
            if (targetIndex !== -1 && targetIndex !== navIndex) {
                // Page exists in history - use browser back/forward to restore from bfcache
                const stepsToGo = targetIndex - navIndex;
                navIndex = targetIndex;
                setCurrentNavIndex(navIndex);
                window.history.go(stepsToGo);
            } else {
                // New page - navigate normally
                window.location.href = targetHref;
            }
            
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // Handle "More" button clicks with smart navigation
    const moreButtons = document.querySelectorAll('.more-btn');
    
    moreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const targetHref = this.getAttribute('href');
            if (!targetHref || targetHref.startsWith('#')) {
                return;
            }
            
            // Prevent default navigation
            e.preventDefault();
            
            // Get target page key
            const targetUrl = new URL(targetHref, window.location.origin).href;
            const targetPageKey = getPageKey(targetUrl);
            const currentPageKey = getPageKey(window.location.href);
            
            // Check if already on this page
            if (targetPageKey === currentPageKey) {
                return;
            }
            
            // Check if target exists in navigation stack
            navStack = getNavStack();
            navIndex = getCurrentNavIndex();
            const targetIndex = navStack.indexOf(targetPageKey);
            
            if (targetIndex !== -1 && targetIndex !== navIndex) {
                // Page exists in history - use browser back/forward to restore from bfcache
                const stepsToGo = targetIndex - navIndex;
                navIndex = targetIndex;
                setCurrentNavIndex(navIndex);
                window.history.go(stepsToGo);
            } else {
                // New page - navigate normally
                window.location.href = targetHref;
            }
        });
    });

});

(function() {
    
    let deferredPrompt = null;
    let bannerDismissedThisSession = false;
    const pwaInstallBanner = document.getElementById('pwaInstallBanner');
    const pwaInstallBtn = document.getElementById('pwaInstallBtn');
    const pwaCloseBtn = document.getElementById('pwaCloseBtn');
    
    if (!pwaInstallBanner || !pwaInstallBtn || !pwaCloseBtn) {
        return;
    }
    
    const PWA_BANNER_INSTALLED_KEY = 'pwa_installed';
    const PWA_PROMPT_AVAILABLE_KEY = 'pwa_prompt_available';
    
    function isPWAInstalled() {
        const installed = localStorage.getItem(PWA_BANNER_INSTALLED_KEY) === 'true' ||
               window.matchMedia('(display-mode: standalone)').matches || 
               window.navigator.standalone === true;
        return installed;
    }
    
    function isIOS() {
        const ios = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        return ios;
    }
    
    function isSafari() {
        const safari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        return safari;
    }
    
    function showBanner(isFallback = false) {
        
        if (isPWAInstalled() || bannerDismissedThisSession) {
            return;
        }
        
        setTimeout(() => {
            pwaInstallBanner.style.display = 'block';
            setTimeout(() => {
                pwaInstallBanner.classList.add('show');
            }, 10);
        }, 3000);
    }
    
    function hideBanner() {
        pwaInstallBanner.classList.remove('show');
        setTimeout(() => {
            pwaInstallBanner.style.display = 'none';
        }, 300);
    }
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        localStorage.setItem(PWA_PROMPT_AVAILABLE_KEY, 'true');
        
        if (window.pwaDebugInfo) {
            window.pwaDebugInfo.beforeInstallPromptFired = true;
        }
        
        showBanner(false);
    });
    
    pwaInstallBtn.addEventListener('click', async () => {
        
        if (!deferredPrompt) {
            return;
        }
        
        deferredPrompt.prompt();
        
        const { outcome } = await deferredPrompt.userChoice;
        
        if (outcome === 'accepted') {
            localStorage.setItem(PWA_BANNER_INSTALLED_KEY, 'true');
            hideBanner();
        } else {
        }
        
        deferredPrompt = null;
    });
    
    pwaCloseBtn.addEventListener('click', () => {
        hideBanner();
        bannerDismissedThisSession = true;
    });
    
    window.addEventListener('appinstalled', () => {
        localStorage.setItem(PWA_BANNER_INSTALLED_KEY, 'true');
        hideBanner();
    });
    
    if (isPWAInstalled()) {
        hideBanner();
    }
    
    setTimeout(() => {
        const promptWasAvailable = localStorage.getItem(PWA_PROMPT_AVAILABLE_KEY) === 'true';
        
        if (!deferredPrompt && !promptWasAvailable && (isIOS() || isSafari())) {
            if (!isPWAInstalled() && !bannerDismissedThisSession) {
                const bannerSubtitle = pwaInstallBanner.querySelector('.pwa-banner-subtitle');
                const installBtn = pwaInstallBtn;
                
                if (bannerSubtitle) {
                    bannerSubtitle.textContent = 'Tap Share → Add to Home Screen';
                }
                
                if (installBtn) {
                    installBtn.style.display = 'none';
                }
                
                showBanner(true);
            }
        } else {
            if (!deferredPrompt && !promptWasAvailable && !isIOS() && !isSafari()) {
            }
        }
    }, 5000);
})();
