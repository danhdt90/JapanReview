<?php 
/*
    Template Name: Home Page
*/
?>

<?php get_header(); ?>
    
  <section id="jr-intro" class="jr-intro position-relative" aria-labelledby="intro-title">
    <div class="container-xxl">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xxl-8 text-left text-lg-center">
          <?= get_field('introducing'); ?>
        </div>
      </div>
    </div>

    <div class="jr-subbar" aria-label="Breadcrumb">
      <div class="container-xxl">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb jr-breadcrumb m-0">
            <li class="breadcrumb-item">
              <a href="#">Top</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
              下層にはヘッダー下部に配置
            </li>
          </ol>
        </nav>
      </div>
    </div>

  </section>

  <section id="jr-news" class="jr-news" aria-labelledby="news-title">
    <?php get_template_part('/template-parts/news/list-news-top', 'page'); ?>
  </section>

  <section id="jr-issues" class="jr-issues py-7 py-lg-9" aria-labelledby="issues-title">
    <?php get_template_part('/template-parts/issue/list-issue-top', 'page'); ?>
  </section>

  <section id="jr-articles" class="jr-articles py-7 py-lg-9" aria-labelledby="articles-title">
    <?php get_template_part('/template-parts/article/list-article-top', 'page'); ?>
  </section>

  <section id="jr-search" class="jr-search" aria-labelledby="search-title">
    <div class="container-xxl text-center">
      <h2 id="search-title" class="jr-sec-title" data-aos="fade-up">Search</h2>

      <!-- Search box -->
      <form class="jr-searchbar mx-auto" role="search" aria-label="Search articles" data-aos="fade-up" data-aos-delay="100">
        <div class="input-group">
          <input type="search" class="form-control" placeholder="Search any word" aria-label="Search any word">
          <button class="btn btn-outline-0 jr-searchbtn" type="submit" aria-label="Search">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>

      <!-- Quick actions -->
      <div class="d-flex flex-column flex-md-row justify-content-center gap-4 gap-md-3 gap-lg-5" data-aos="fade-up" data-aos-delay="200">
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
      <h3 class="jr-subtitle" data-aos="fade-up">Year of Publication</h3>
      <ul class="jr-tagcloud year-group" data-aos="fade-up">
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
      <h3 class="jr-subtitle" data-aos="fade-up">Type of Publication</h3>
      <ul class="jr-tagcloud" data-aos="fade-up">
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
      <h3 class="jr-subtitle" data-aos="fade-up">Keyword</h3>
      <ul class="jr-tagcloud" data-aos="fade-up">
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

<?php get_template_part('/template-parts/components/contact', 'form'); ?>
<?php get_footer(); ?>