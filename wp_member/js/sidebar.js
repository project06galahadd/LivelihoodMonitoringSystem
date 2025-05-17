// Sidebar functionality enhancement
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle sidebar toggle
    $('.sidebar-toggle').on('click', function() {
        $('body').toggleClass('sidebar-collapse');
        // Store sidebar state in localStorage
        localStorage.setItem('memberSidebarState', $('body').hasClass('sidebar-collapse') ? 'collapsed' : 'expanded');
    });

    // Restore sidebar state from localStorage
    const savedState = localStorage.getItem('memberSidebarState');
    if (savedState === 'collapsed') {
        $('body').addClass('sidebar-collapse');
    }

    // Handle navigation links
    $('.nav-link').on('click', function(e) {
        const href = $(this).attr('href');
        if (href && href !== '#' && href !== window.location.pathname) {
            e.preventDefault();
            window.location.href = href;
        }
    });

    // Handle submenu toggling with smooth animations
    $('.nav-item.has-treeview > .nav-link').on('click', function(e) {
        const $this = $(this);
        const $treeview = $this.next('.nav-treeview');
        
        if ($treeview.length) {
            e.preventDefault();
            
            if ($this.parent().hasClass('menu-open')) {
                $treeview.slideUp(200, function() {
                    $this.parent().removeClass('menu-open');
                    $this.find('i.right').css('transform', 'translateY(-50%) rotate(0deg)');
                });
            } else {
                $this.parent().addClass('menu-open');
                $treeview.slideDown(200, function() {
                    $this.find('i.right').css('transform', 'translateY(-50%) rotate(-90deg)');
                });
            }
        }
    });

    // Add hover effects for better interactivity
    $('.nav-item').hover(
        function() {
            $(this).addClass('hovered');
        },
        function() {
            $(this).removeClass('hovered');
        }
    );

    // Handle active state updates
    function updateActiveState() {
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop();
        
        // Handle special case for livelihood_records.php
        if (currentPage === 'livelihood_records.php') {
            $('.nav-link[href="livelihood_records.php"]').addClass('active');
            $('.nav-link[href="livelihood_records.php"]').parents('.nav-item').addClass('active');
            $('.nav-link[href="livelihood_records.php"]').parents('.has-treeview').addClass('menu-open');
            return;
        }
        
        // Handle other pages
        $('.nav-link').each(function() {
            const linkPath = $(this).attr('href');
            if (linkPath && currentPath.endsWith(linkPath)) {
                $(this).addClass('active');
                $(this).parents('.nav-item').addClass('active');
                $(this).parents('.has-treeview').addClass('menu-open');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    // Update active state on page load and hash change
    updateActiveState();
    $(window).on('hashchange', updateActiveState);

    // Add smooth scrolling for internal links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 50
            }, 500);
        }
    });

    // Fix for preventing click events from bubbling up to parent elements
    $(document).on('click', '.nav-link', function(e) {
        e.stopPropagation();
    });

    // Fix for preventing hover effects from triggering when clicking
    $(document).on('click', '.nav-item', function() {
        $(this).removeClass('hovered');
    });

    // Fix for proper submenu handling
    $(document).on('click', '.has-treeview .nav-link', function(e) {
        const $this = $(this);
        const $treeview = $this.next('.nav-treeview');
        
        if ($treeview.length) {
            e.preventDefault();
            
            if ($this.parent().hasClass('menu-open')) {
                $treeview.slideUp(200, function() {
                    $this.parent().removeClass('menu-open');
                });
            } else {
                $treeview.slideDown(200, function() {
                    $this.parent().addClass('menu-open');
                });
            }
        }
    });
        // Update active state on page load and hash change
        updateActiveState();
        $(window).on('hashchange', updateActiveState);
    });

    // Handle active state updates
    function updateActiveState() {
        const currentPath = window.location.pathname;
        $('.nav-sidebar .nav-link').each(function() {
            const linkPath = $(this).attr('href');
            if (linkPath && currentPath.endsWith(linkPath)) {
                $(this).addClass('active');
                $(this).parents('.nav-item').addClass('active');
                $(this).parents('.has-treeview').addClass('menu-open');
            } else {
                $(this).removeClass('active');
            }
        });
    }

    // Update active state on page load and hash change
    updateActiveState();
    $(window).on('hashchange', updateActiveState);
;$(document).ready(function() {
    // All your event handlers and functions
    // ...
    // Only one closing brace at the end
});  // This should be the only closing brace
