<?php
    $desktoop_banenr = IMAGE_PATH . '/img-about.jpg';
    $mobile_banner = IMAGE_PATH . '/img-about-sp.jpg';
    $volume = pods_field('volume');

    // Helper function to extract value from array or string
    $extract_value = function($field) {
        if (is_array($field)) {
            return !empty($field[0]) ? $field[0] : '';
        }
        return $field;
    };
    
    // Extract volume value
    $volume_value = $extract_value($volume);
    $cover_image_iss = '';
    // Query issue by volume (ACF field)
    if (!empty($volume_value)) {
        $issue = get_posts([
            'post_type'      => 'issue',
            'posts_per_page' => 1,
            'post_status'    => 'publish',
            'posts_per_page'    => -1,
            'meta_key'      => 'volume',
            'meta_value'    => $volume_value
        ]);
        if (!empty($issue)) {
            $issue_id = $issue[0]->ID;
            $cover_image_iss = get_field('cover_image', $issue_id);
        }
    }
    
    $articleDetail = [
        'main_title' => pods_field('main_title'),
        'other_title' => pods_field('other_title'),
        'group_author' => pods_field('group_author'),
        // 'resource_type' => pods_field('resource_type'), 
        'doi' => pods_field('doi'), 
        'content_description' => pods_field('content_description'),
        'volume' => pods_field('volume'), 
        'publication_date' => pods_field('publication_date'), 
        'start_page' => pods_field('start_page'), 
        'end_page' => pods_field('end_page'), 
        'abstract' => pods_field('abstract'), // Repeater field (array)
        'external_link_title' => pods_field('external_link_title'), 
        'external_link' => pods_field('external_link'), 
    ];

?>

<section id="jr-about" class="jr-about" aria-labelledby="Articles-title">
    <div class="container">
        <h1 id="Articles-title" class="jr-sec-title jr-sec-title-sub">Articles</h1>
    </div>

    <!-- figure ra ngoài container -->
    <figure class="about-hero__figure">
        <?php if($desktoop_banenr) : ?>
            <img src="<?= $desktoop_banenr; ?>" alt="About artwork" class="about-hero__img d-none d-md-block">
        <?php endif; ?>
        <?php if($mobile_banner) : ?>
            <img src="<?= $mobile_banner; ?>" alt="About artwork" class="about-hero__img d-block d-md-none">
        <?php endif; ?>
    </figure>

    <section id="jr-article" class="jr-article p-0" aria-labelledby="art-title">
        <div class="container">
            <div class="row g-5">
                <!-- Cover -->
                <div class="col-12 col-lg-5">
                    <figure class="art-cover ratio ratio-3x4">
                        <?php if(has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('full', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
                        <?php elseif($cover_image_iss): ?>
                            <img src="<?= esc_url($cover_image_iss['url']); ?>" alt="<?= esc_attr($cover_image_iss['title']); ?>" loading="lazy">
                        <?php else: ?>
                            <img src="<?= get_template_directory_uri(); ?>/assets/images/no-image.jpg" alt="No image available" loading="lazy">
                        <?php endif; ?>
                    </figure>
                </div>

                <!-- Content -->
                <div class="col-12 col-lg-7">
                    <!-- Display post title first -->
                     <p class="art-section"><?php the_title(); ?></p>
                    
                    <?php 
                    // Display main_title (repeater)
                    if (!empty($articleDetail['main_title']) && is_array($articleDetail['main_title'])): 
                        foreach ($articleDetail['main_title'] as $main_title): ?>
                            <p class="art-section"><?php echo esc_html($main_title); ?></p>
                        <?php endforeach;
                    endif; 
                    ?>

                    <?php 
                    // Display other_title (repeater) - each on new line
                    if (!empty($articleDetail['other_title']) && is_array($articleDetail['other_title'])): 
                        foreach ($articleDetail['other_title'] as $other_title): ?>
                            <h2 class="art-title"><?php echo esc_html($other_title); ?></h2>
                        <?php endforeach;
                    elseif (!empty($articleDetail['other_title'])): ?>
                        <h2 class="art-title"><?php echo esc_html($articleDetail['other_title']); ?></h2>
                    <?php endif; ?>

                    <ul class="art-meta">
                        <?php 
                        // Display group_author (repeater)
                        if (!empty($articleDetail['group_author']) && is_array($articleDetail['group_author'])): 
                            foreach ($articleDetail['group_author'] as $author): ?>
                                <li><?php echo esc_html($author); ?></li>
                            <?php endforeach;
                        endif; 
                        
                        // Display volume and page info
                        $vol_value = $extract_value($articleDetail['volume']);
                        $start_page_value = $extract_value($articleDetail['start_page']);
                        $end_page_value = $extract_value($articleDetail['end_page']);
                        $pub_date_value = $extract_value($articleDetail['publication_date']);
                        
                        if (!empty($vol_value) || !empty($start_page_value) || !empty($end_page_value)):
                            echo '<li>';
                            if (!empty($vol_value)) echo 'Vol.' . esc_html($vol_value);
                            if (!empty($pub_date_value)) echo ' (' . esc_html($pub_date_value) . ')';
                            if (!empty($start_page_value) || !empty($end_page_value)) {
                                echo ' pp. ';
                                if (!empty($start_page_value)) echo esc_html($start_page_value);
                                if (!empty($start_page_value) && !empty($end_page_value)) echo '–';
                                if (!empty($end_page_value)) echo esc_html($end_page_value);
                            }
                            echo '</li>';
                        endif;
                        
                        // Display resource_type
                        $resource_type_value = $extract_value($articleDetail['resource_type']);
                        if (!empty($resource_type_value)): ?>
                            <li><?php echo esc_html($resource_type_value); ?></li>
                        <?php endif; 
                        
                        // Display DOI
                        $doi_value = $extract_value($articleDetail['doi']);
                        if (!empty($doi_value)): ?>
                            <li><span class="link-underline" target="_blank" rel="noopener">DOI: <?php echo esc_html($doi_value); ?></span></li>
                        <?php endif;
                        
                        // Display public 
                        
                        // Display publication date
                        if (!empty($articleDetail['content_description']) && is_array($articleDetail['content_description'])): ?>
                            <?php foreach ($articleDetail['content_description'] as $description): ?>
                            <li><?php echo wp_kses_post($description); ?></li>
                             <?php endforeach; ?>
                        <?php endif; ?>
                        <?php
                        // Display external link if both title and link are available
                        $external_link_title = $extract_value($articleDetail['external_link_title']);
                        $external_link = $extract_value($articleDetail['external_link']);
                        if (!empty($external_link_title) && !empty($external_link)): ?>
                            <li>
                                <a href="<?php echo esc_url($external_link); ?>" target="_blank" rel="noopener" class="  font-weight-bold " style="font-weight:600;text;text-decoration: none;">
                                    <?php echo esc_html($external_link_title); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Abstract -->
            <div class="row mt-5">
                <div class="col-12 col-lg-12">                    
                    <?php 
                    // Display abstract (repeater)
                    if (!empty($articleDetail['abstract']) && is_array($articleDetail['abstract'])): ?>
                        <h3 class="art-block-label">Abstract</h3>
                        <div class="art-abstract">
                            <?php foreach ($articleDetail['abstract'] as $abstract): ?>
                                <p><?php echo wp_kses_post($abstract); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                    <div class="art-keywords mt-4">
                        <div class="mb-2">Keyword</div>
                        <ul class="jr-tagcloud justify-content-start ms-0">
                            <?php 
                            // Display keywords from taxonomy
                            $keywords = get_the_terms(get_the_ID(), 'keywords_article');
                            if ($keywords && !is_wp_error($keywords)): 
                                foreach ($keywords as $keyword): ?>
                                    <li>
                                        <a href="<?php echo esc_url(get_term_link($keyword)); ?>">
                                            ＃<?php echo esc_html($keyword->name); ?>
                                        </a>
                                    </li>
                                <?php endforeach;
                            else: ?>
                                <li>No keywords</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="mt-5 text-center">
                        <a href="<?php echo esc_url( home_url('/articles') ); ?>" class="btn btn-viewmore" id="btn-back-index" data-back="<?php echo esc_attr( home_url('/articles') ); ?>">
                            <span>Back to Index</span>
                            <svg class="btn-circle" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11.5" stroke="white" />
                                <path d="M13.25 16.3692L12.375 15.4018L14.5938 13.0334H7V11.6991H14.5938L12.375 9.33067L13.25 8.36328L17 12.3663L13.25 16.3692Z" fill="white" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</section>

<section id="jr-search-dual" class="jr-search-dual" aria-labelledby="search2-title">
    <div class="container-xxl text-center">
        <h2 id="search2-title" class="jr-sec-title">Search</h2>

        <div class="row g-5 justify-content-center mb-4">
            <div class="col-12 col-lg-5">
                <form class="jr-searchbar mx-auto" id="form-keyword" role="search" aria-label="Search by keyword" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <div class="input-group">
                        <input type="search" class="form-control" name="s" value="<?php echo get_search_query(); ?>" placeholder="Search any word" aria-label="Search any word">
                        <button class="btn btn-outline-0 jr-searchbtn" type="submit" aria-label="Search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30" viewBox="0 0 29 30" fill="none">
                                <path d="M26.7444 29.0215L16.5944 18.864C15.7889 19.5089 14.8625 20.0195 13.8153 20.3957C12.7681 20.7719 11.6537 20.96 10.4722 20.96C7.54537 20.96 5.06829 19.9456 3.04097 17.9168C1.01366 15.888 0 13.409 0 10.48C0 7.55098 1.01366 5.07205 3.04097 3.04323C5.06829 1.01441 7.54537 0 10.4722 0C13.3991 0 15.8762 1.01441 17.9035 3.04323C19.9308 5.07205 20.9444 7.55098 20.9444 10.48C20.9444 11.6624 20.7565 12.7775 20.3806 13.8255C20.0046 14.8735 19.4944 15.8006 18.85 16.6068L29 26.7643L26.7444 29.0215ZM10.4722 17.7354C12.4861 17.7354 14.1979 17.03 15.6076 15.6192C17.0174 14.2085 17.7222 12.4954 17.7222 10.48C17.7222 8.46462 17.0174 6.75154 15.6076 5.34077C14.1979 3.93 12.4861 3.22462 10.4722 3.22462C8.45833 3.22462 6.74653 3.93 5.33681 5.34077C3.92708 6.75154 3.22222 8.46462 3.22222 10.48C3.22222 12.4954 3.92708 14.2085 5.33681 15.6192C6.74653 17.03 8.45833 17.7354 10.4722 17.7354Z" fill="#817E7E" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <h3 class="jr-subtitle mt-4 mb-3">Keyword</h3>
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
</section>