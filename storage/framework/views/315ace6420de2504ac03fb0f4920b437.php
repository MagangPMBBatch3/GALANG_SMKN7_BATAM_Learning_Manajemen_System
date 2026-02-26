



<?php
    $currentPath = request()->path();
    $active = $activeNav ?? '';

    // Auto-detect active tab from URL if not explicitly set
    if (empty($active)) {
        if ($currentPath === 'admin') $active = 'dashboard';
        elseif (str_contains($currentPath, 'admin/users')) $active = 'users';
        elseif (str_contains($currentPath, 'admin/courses')) $active = 'courses';
        elseif (str_contains($currentPath, 'admin/quizzes')) $active = 'quizzes';
        elseif (str_contains($currentPath, 'admin/categories')) $active = 'categories';
        elseif (str_contains($currentPath, 'admin/enrollments')) $active = 'enrollments';
        elseif (str_contains($currentPath, 'admin/payments')) $active = 'payments';
        elseif (str_contains($currentPath, 'admin/badges')) $active = 'badges';
        elseif (str_contains($currentPath, 'admin/notifications')) $active = 'notifications';
        elseif (str_contains($currentPath, 'admin/grading')) $active = 'grading';
        elseif (str_contains($currentPath, 'admin/progress')) $active = 'progress';
        else $active = 'dashboard';
    }

    $navLinks = [
        ['key' => 'dashboard',     'href' => '/admin',                'icon' => 'fa-tachometer-alt',  'label' => 'Dashboard'],
        ['key' => 'users',         'href' => '/admin/users',          'icon' => 'fa-users',           'label' => 'Pengguna'],
        ['key' => 'courses',       'href' => '/admin/courses',        'icon' => 'fa-graduation-cap',  'label' => 'Kursus'],
        ['key' => 'quizzes',       'href' => '/admin/quizzes',        'icon' => 'fa-clipboard-list',  'label' => 'Quiz'],
        ['key' => 'categories',    'href' => '/admin/categories',     'icon' => 'fa-tags',            'label' => 'Kategori'],
        ['key' => 'enrollments',   'href' => '/admin/enrollments',    'icon' => 'fa-user-check',      'label' => 'Pendaftaran'],
        ['key' => 'payments',      'href' => '/admin/payments',       'icon' => 'fa-credit-card',     'label' => 'Pembayaran'],
        ['key' => 'badges',        'href' => '/admin/badges',         'icon' => 'fa-medal',           'label' => 'Lencana'],
        ['key' => 'notifications', 'href' => '/admin/notifications',  'icon' => 'fa-bell',            'label' => 'Notifikasi'],
        ['key' => 'grading',       'href' => '/admin/grading',        'icon' => 'fa-check-double',    'label' => 'Penilaian'],
    ];
?>

<div class="mb-8 overflow-x-auto">
    <nav class="flex space-x-1 min-w-max" aria-label="Admin Navigation">
        <?php $__currentLoopData = $navLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = ($active === $link['key']);
            ?>
            <a href="<?php echo e($link['href']); ?>"
               class="whitespace-nowrap py-2 px-3 border-b-2 font-medium text-sm transition-colors duration-150
                      <?php echo e($isActive
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                <i class="fas <?php echo e($link['icon']); ?> mr-1.5"></i><?php echo e($link['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</div>
<?php /**PATH C:\xampp\htdocs\maxcourse\resources\views\admin\partials\navbar.blade.php ENDPATH**/ ?>