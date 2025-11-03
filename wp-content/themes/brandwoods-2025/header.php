<!DOCTYPE html>
<html lang="jp">

<?php include('includes/head.php'); ?>

<body>
    <header class="jr-header">
        <nav class="navbar navbar-expand-lg" aria-label="Primary">
            <div class="container-xxl position-relative">

                <!-- Brand / Logo -->
                <a class="navbar-brand jr-brand d-flex flex-column" href="/">
                    <img src="<?= IMAGE_PATH; ?>/logo.svg" alt="Japan Review Logo" class="jr-brand__logo" width="115">
                </a>

                <!-- Nav (center) + Contact (right) -->
                
                <div class="collapse navbar-collapse" id="jrNav">
                    <?php 
                        $menu = brandwoods_render_menu('menu_header');
                        global $wp;
                        $current_url = home_url( add_query_arg( [], $wp->request ) );
                        if ( $menu !== false && count($menu) > 0) :
                            ?>
                                <ul class="navbar-nav align-items-lg-center gap-lg-4 jr-nav">
                                    <?php
                                        foreach ($menu as $index => $items) :
                                            $active_class = (untrailingslashit($items->url) == untrailingslashit($current_url)) ? 'active' : '';
                                            ?>
                                                <li class="nav-item <?=implode(" ", $items->classes);?>"><a target="<?= $items->target;?>" class="nav-link <?= $active_class; ?>" href="<?= $items->url; ?>"><?= $items->title; ?></a></li>
                                            <?php
                                        endforeach;
                                    ?>
                                    <li class="nav-item d-lg-none">
                                        <a class="btn btn-contact w-100 mt-3" href="#">
                                        <i class="bi bi-envelope me-2"></i>Contact
                                        </a>
                                    </li>
                                </ul>
                            <?php
                        endif;
                    ?>
                </div>

                <!-- Contact button (right, only >= lg) -->
                <div class="d-none d-lg-block ms-3">
                    <a class="btn btn-contact" href="#"><i class="bi bi-envelope me-2"></i>Contact</a>
                </div>

                <!-- Toggler (Hamburger) -->
                <button class="navbar-toggler shadow-none border-0 jr-burger" type="button"
                    data-bs-toggle="collapse" data-bs-target="#jrNav" aria-controls="jrNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                </button>
            </div>
        </nav>
    </header>

