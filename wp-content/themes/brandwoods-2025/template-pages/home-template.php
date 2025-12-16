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
      <h2 id="search-title" class="jr-sec-title">Search</h2>

      <!-- Search box -->
      <form class="jr-searchbar mx-auto" role="search" aria-label="Search articles" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <div class="input-group">
          <input type="search" class="form-control" name="s" value="<?php echo get_search_query(); ?>" placeholder="Search any word" aria-label="Search any word">
          <button class="btn btn-outline-0 jr-searchbtn" type="submit" aria-label="Search">
            <i class="bi bi-search"></i>
          </button>
        </div>
      </form>

      <!-- Quick actions -->
      <div class="d-flex flex-column flex-md-row justify-content-center gap-4 gap-md-3 gap-lg-5">
        <?php 
        // Get Early Access term link
        $early_access_term = get_term_by('slug', 'early-access', 'article_early_access');
        $early_access_link = $early_access_term ? get_term_link($early_access_term) : '#';
        ?>
        <a href="<?php echo esc_url($early_access_link); ?>" class="btn jr-pill jr-pill-cranberry">
          <span>Early Access Articles</span>
          <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
        </a>

        <?php 
        // Get Special Issue term link
        $special_issue_term = get_term_by('slug', 'special-issue', 'issue_special');
        $special_issue_link = $special_issue_term ? get_term_link($special_issue_term) : '#';
        ?>
        <a href="<?php echo esc_url($special_issue_link); ?>" class="btn jr-pill jr-pill-navy">
          <span>Special Issues</span>
          <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
        </a>
      </div>

      <!-- Year of Publication -->
      <h3 class="jr-subtitle">Year of Publication</h3>
      <ul class="jr-tagcloud year-group">
        <?php 
        // Get all publication years from taxonomy
        $all_years = get_terms(array(
            'taxonomy'   => 'article_year',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'DESC', // Newest first
        ));
        
        if (!empty($all_years) && !is_wp_error($all_years)): 
            foreach ($all_years as $year): ?>
                <li>
                    <a href="<?php echo esc_url(get_term_link($year)); ?>">
                        #<?php echo esc_html($year->name); ?>
                    </a>
                </li>
            <?php endforeach;
        else: ?>
            <li><a href="#">No years available</a></li>
        <?php endif; ?>
      </ul>

      <!-- Type of Publication -->
      <h3 class="jr-subtitle">Type of Publication</h3>
      <ul class="jr-tagcloud">
        <?php 
        // Get all publication types from taxonomy
        $all_publication_types = get_terms(array(
            'taxonomy'   => 'publication_type',
            'hide_empty' => true, // Only show publication types that have articles
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));
        
        if (!empty($all_publication_types)): 
            foreach ($all_publication_types as $publication_type): ?>
                <li>
                    <a href="<?php echo esc_url(get_term_link($publication_type)); ?>">
                        #<?php echo esc_html($publication_type->name); ?>
                    </a>
                </li>
            <?php endforeach;
        else: ?>
            <li><a href="#">No genres available</a></li>
        <?php endif; ?>
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
  </section>

<?php get_template_part('/template-parts/components/contact', 'form'); ?>
<?php get_footer(); ?>