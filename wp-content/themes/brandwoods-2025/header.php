<!DOCTYPE html>
<html lang="jp">

<?php include('includes/head.php'); ?>

<body>
    <header class="jr-header">
        <nav class="navbar navbar-expand-lg" aria-label="Primary">
            <div class="container-xxl position-relative">

                <!-- Brand / Logo -->
                <a class="navbar-brand jr-brand d-flex flex-column" href="<?= esc_url( home_url( '/' ) ); ?>">
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
                <!-- <button class="navbar-toggler shadow-none border-0 jr-burger" type="button"
                    data-bs-toggle="collapse" data-bs-target="#jrNav" aria-controls="jrNav"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                </button> -->

                <!-- Toggler (Search) -->
                <button class="navbar-toggler shadow-none border-0 jr-burger"
                    type="button"
                    aria-controls="jrSearch" aria-expanded="false" aria-label="Open search"
                    data-action="toggle-search">
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                    <span class="jr-burger-bar"></span>
                </button>
            </div>
        </nav>
    </header>

    <div class="jr-search-box" id="jrSearch" aria-hidden="true" role="dialog" aria-modal="true">
    <!-- <div class="jr-search__backdrop" data-close-search></div> -->

        <section id="jr-search" class="jr-search" aria-labelledby="search-title">
            <div class="container-xxl text-center">
            <h2 id="search-title" class="jr-sec-title">Search</h2>

            <!-- Search box -->
            <form class="jr-searchbar mx-auto" role="search" aria-label="Search articles" data-aos-delay="100">
                <div class="input-group">
                <input type="search" class="form-control" placeholder="Search any word" aria-label="Search any word">
                <button class="btn btn-outline-0 jr-searchbtn" type="submit" aria-label="Search">
                    <i class="bi bi-search"></i>
                </button>
                </div>
            </form>

            <!-- Quick actions -->
            <div class="d-flex flex-column flex-md-row justify-content-center gap-4 gap-md-3 gap-lg-5 mb-5" data-aos-delay="200">
                <a href="#" class="btn jr-pill jr-pill-cranberry">
                <span>Early Access Articles</span>
                <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
                </a>

                <a href="#" class="btn jr-pill jr-pill-navy">
                <span>Special Issues</span>
                <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
                </a>
            </div>

            <!-- Year of Publication -->
            <h3 class="jr-subtitle">Year of Publication</h3>
            <ul class="jr-tagcloud year-group">
                <li><a href="#">#2025</a></li>
                <li><a href="#">#2024</a></li>
                <li><a href="#">#2023</a></li>
                <li><a href="#">#2022</a></li>
                <li><a href="#">#2021</a></li>
                <li><a href="#">#2020</a></li>
                <li><a href="#">#2015</a></li>
                <li><a href="#">#2014</a></li>
                <li><a href="#">#2013</a></li>
                <li><a href="#">#2012</a></li>
                <li><a href="#">#2011</a></li>
                <li><a href="#">#2010</a></li>
            </ul>

            <!-- Type of Publication -->
            <h3 class="jr-subtitle">Type of Publication</h3>
            <ul class="jr-tagcloud">
                <li><a href="#">#journal article</a></li>
                <li><a href="#">#review</a></li>
                <li><a href="#">#research note</a></li>
                <li><a href="#">#book review</a></li>
                <li><a href="#">#editorial</a></li>
                <li><a href="#">#essay</a></li>
                <li><a href="#">#translation</a></li>
                <li><a href="#">#interview</a></li>
                <li><a href="#">#obituary</a></li>
                <li><a href="#">#special issue</a></li>
            </ul>

            <!-- Keyword -->
            <h3 class="jr-subtitle">Keyword</h3>
            <ul class="jr-tagcloud">
                <?php 
                // Get all keywords from taxonomy
                $all_keywords = get_terms(array(
                    'taxonomy'   => 'keywords_article',
                    'hide_empty' => true, // Only show keywords that have articles
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                ));
                
                if (!empty($all_keywords) && !is_wp_error($all_keywords)): 
                    foreach ($all_keywords as $keyword): ?>
                        <li>
                            <a href="<?php echo esc_url(get_term_link($keyword)); ?>">
                                #<?php echo esc_html($keyword->name); ?>
                            </a>
                        </li>
                    <?php endforeach;
                else: ?>
                    <li><a href="#">No keywords available</a></li>
                <?php endif; ?>
            </ul>
            </div>

            <button type="button" class="btn btn-contact px-4">
                <svg width="14" height="11" viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.5779 0H0.422133C0.189273 0 0 0.189273 0 0.422543V1.25355L7 5.94428L14 1.25311V0.422543C14 0.189273 13.8107 0 13.5779 0Z" fill="white" />
                    <path d="M0 2.98872V8.55432C0 9.48812 0.755809 10.2445 1.68932 10.2445H12.3107C13.2442 10.2445 14 9.48812 14 8.55432V2.98828L7 7.68294L0 2.98872Z" fill="white" />
                </svg>
                Contact
            </button>

        </section>

    </div>

