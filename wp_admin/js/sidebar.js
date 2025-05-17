// Sidebar functionality enhancement
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle sidebar toggle
    $('.sidebar-toggle').on('click', function() {
        $('body').toggleClass('sidebar-collapse');
        // Store sidebar state in localStorage
        localStorage.setItem('sidebarState', $('body').hasClass('sidebar-collapse') ? 'collapsed' : 'expanded');
    });

    // Restore sidebar state from localStorage
    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'collapsed') {
        $('body').addClass('sidebar-collapse');
    }

    // Smooth scrolling for navigation links
    $('.nav-sidebar .nav-link').on('click', function(e) {
        const href = $(this).attr('href');
        if (href && href !== '#') {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $(href).offset().top - 50
            }, 500);
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
    $('.nav-sidebar .nav-item').hover(
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
});
