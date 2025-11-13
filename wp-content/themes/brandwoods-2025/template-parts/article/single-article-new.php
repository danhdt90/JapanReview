<?php
    $desktoop_banenr = get_field('desktop_banner');
    $mobile_banner = get_field('mobile_banner');
    $volume = pods_field('volume');

    // Helper function to extract value from array or string
    $extract_value = function($field) {
        if (is_array($field)) {
            return !empty($field[0]) ? $field[0] : '';
        }
        return $field;
    };

    $volume_value = $extract_value($volume);
    $cover_image_iss = '';
    $issue = get_posts([
        'post_type'      => 'issue',
        'posts_per_page' => 1,
        'meta_key'       => 'volume',
        'meta_value'     => $volume,
    ]);


    if (!empty($issue)) {
        $issue_id = $issue[0]->ID;
        $cover_image_iss = get_field('cover_image', $issue_id);
    }

?>

<section id="jr-about" class="jr-about" aria-labelledby="Articles-title">
    <div class="container">
        <h1 id="Articles-title" class="jr-sec-title jr-sec-title-sub">Articles</h1>
    </div>

    <!-- figure ra ngoài container -->
    <figure class="about-hero__figure">
        <?php if($desktoop_banenr) : ?>
            <img src="<?= $desktoop_banenr['url'] ?>" alt="About artwork" class="about-hero__img d-none d-md-block">
        <?php endif; ?>
        <?php if($mobile_banner) : ?>
            <img src="<?= $mobile_bannere ?>" alt="About artwork" class="about-hero__img d-block d-md-none">
        <?php endif; ?>
    </figure>

    <section id="jr-article" class="jr-article p-0" aria-labelledby="art-title">
        <div class="container">
            <div class="row g-5">
                <!-- Cover -->
                <?php $cover_image = pods_field('cover_image'); ?>
                
                <div class="col-12 col-lg-5">
                    <figure class="art-cover ratio ratio-3x4">
                        <?php if(!empty($cover_image)) : ?>
                            <img src="<?php echo esc_url($cover_image['guid']); ?>" alt="<?php echo esc_attr($cover_image['post_title']); ?>" loading="lazy">
                        <?php else: ?>
                            <img src="<?= $cover_image_iss ? esc_url($cover_image_iss['url']) : ''; ?>" alt="<?= $cover_image_iss ? esc_url($cover_image_iss['title']) : ''; ?>" loading="lazy">
                        <?php endif; ?>
                    </figure>
                </div>

                <!-- Content -->
                <div class="col-12 col-lg-7">
                    <p class="art-section">&lt;Special Section&gt; Domain Shinto in Tokugawa Japan</p>

                    <h1 id="art-title" class="art-title">
                        Domain Shinto as a Testing Ground of Early Modern Shinto
                    </h1>

                    <ul class="art-meta">
                        <li>Bernhard Scheid</li>
                        <li>Vol.39 (2024) pp. 17–36</li>
                        <li>journal article</li>
                        <li><a class="link-underline" href="https://doi.org/10.69307/japanreview.39.0_7" target="_blank" rel="noopener">DOI: 10.69307/japanreview.39.0_7</a></li>
                        <li>Early Access Publishing date: 2024/06/28</li>
                    </ul>
                </div>
            </div>

            <!-- Abstract -->
            <div class="row mt-5">
                <div class="col-12 col-lg-12">
                    <h3 class="art-block-label">Abstract</h3>
                    <div class="art-abstract">
                        <p>
                            This article develops the introduction to this Special Section by addressing the usefulness and scope of the term “Domain Shinto.” It starts with a discussion of the terauke system and the question of how anti-Christian religious inspection was related to Domain Shinto. The article goes on to qualify the alleged influence of Yoshida Shinto on Domain Shinto, demonstrating that this influence was only indirect and that the common term for Yoshida Shinto, yuiitsu shintō, did not always signify the teaching of the Yoshida. The article finally discusses the quest of local lords for ritual autonomy as a consistent feature of the various forms of Domain Shinto.
                        </p>
                    </div>

                    <div class="art-keywords mt-4">
                        <div class="mb-2">Keyword</div>
                        <ul class="jr-tagcloud justify-content-start ms-0">
                            <li><a href="#">＃Japan</a></li>
                            <li><a href="#">＃China</a></li>
                            <li><a href="#">＃USA</a></li>
                            <li><a href="#">＃Spain</a></li>
                            <li><a href="#">＃India</a></li>
                            <li><a href="#">＃Hongkong</a></li>
                            <li><a href="#">＃Tokyo</a></li>
                            <li><a href="#">＃Chiba</a></li>
                            <li><a href="#">＃Osaka</a></li>
                            <li><a href="#">＃Kyoto</a></li>
                            <li><a href="#">＃Nara</a></li>
                            <li><a href="#">＃Kobe</a></li>
                            <li><a href="#">＃Hokkaido</a></li>
                            <li><a href="#">＃Okinawa</a></li>
                        </ul>
                    </div>

                    <div class="mt-5 text-center">
                        <a href="index.php" class="btn btn-viewmore" id="btn-back-index" data-back="index.php">
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
                <form class="jr-searchbar mx-auto" id="form-keyword" role="search" aria-label="Search by keyword">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" placeholder="Search any word" aria-label="Search any word">
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
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
            <li><a href="#">#journal article</a></li>
        </ul>
    </div>
</section>