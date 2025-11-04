<?php 
/*
    Template Name: Home Page
*/
?>

<?php get_header(); ?>
    
<section id="jr-intro" class="jr-intro position-relative" aria-labelledby="intro-title">
    <div class="container-xxl">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xxl-8 text-center">
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

  <section id="jr-issues" class="jr-issues py-7 py-lg-9" aria-labelledby="issues-title">
    <div class="container text-center">
      <h2 id="issues-title" class="jr-sec-title" data-aos="fade-up">Latest Issues</h2>

      <div class="row g-5 justify-content-center">
        <!-- Issue 1 -->
        <div class="col-10 col-sm-6 col-lg-4">
          <a class="issue-card" href="articles-list.php" data-aos="fade-up" data-aos-delay="100">
            <figure class="m-0">
              <div class="issue-cover ratio ratio-3x4">
                <img src="assets/images/issues/issues01.png" class="img-fluid" alt="Japan Review No.34 (2019)" loading="lazy">
              </div>
            </figure>
          </a>
        </div>

        <!-- Issue 2 -->
        <div class="col-10 col-sm-6 col-lg-4">
          <a class="issue-card" href="articles-list.php" data-aos="fade-up" data-aos-delay="200">
            <figure class="m-0">
              <div class="issue-cover ratio ratio-3x4">
                <img src="assets/images/issues/issues02.png" class="img-fluid" alt="Japan Review No.27 (2014)" loading="lazy">
              </div>
            </figure>
          </a>
        </div>

        <!-- Issue 3 -->
        <div class="col-10 col-sm-6 col-lg-4">
          <a class="issue-card" href="articles-list.php" data-aos="fade-up" data-aos-delay="300">
            <figure class="m-0">
              <div class="issue-cover ratio ratio-3x4">
                <img src="assets/images/issues/issues03.png" class="img-fluid" alt="Japan Review Vol.38 (2023)" loading="lazy">
              </div>
            </figure>
          </a>
        </div>
      </div>

      <!-- View more -->
      <div class="mt-5">
        <a href="articles-list.php" class="btn btn-viewmore" data-aos="fade-up" data-aos-delay="400">
          <span>View More</span>
          <span class="btn-circle" aria-hidden="true">
            <i class="bi bi-arrow-right-short"></i>
          </span>
        </a>
      </div>
    </div>
  </section>

  <section id="jr-news" class="jr-news py-7 py-lg-9" aria-labelledby="news-title">
    <?php get_template_part('/template-parts/components/list-news-top', 'page'); ?>
  </section>

  <section id="jr-search" class="jr-search py-7 py-lg-9" aria-labelledby="search-title">
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
      <div class="d-flex flex-column flex-md-row justify-content-center gap-4 gap-md-3 gap-lg-5 mb-5" data-aos="fade-up" data-aos-delay="200">
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
        <li><a href="#">#anthropology</a></li>
        <li><a href="#">#history</a></li>
        <li><a href="#">#literature</a></li>
        <li><a href="#">#religion</a></li>
        <li><a href="#">#linguistics</a></li>
        <li><a href="#">#politics</a></li>
        <li><a href="#">#economics</a></li>
        <li><a href="#">#culture</a></li>
        <li><a href="#">#international relations</a></li>
        <li><a href="#">#law</a></li>
        <li><a href="#">#art</a></li>
        <li><a href="#">#philosophy</a></li>
      </ul>
    </div>
  </section>

  <section id="jr-articles" class="jr-articles py-7 py-lg-9" aria-labelledby="articles-title">
    <div class="container-xxl position-relative">

      <h2 id="articles-title" class="jr-sec-title text-center" data-aos="fade-up">Latest Articles</h2>

      <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

          <!-- 1 item -->
          <article class="art-item" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-3 flex-nowrap">
              <!-- thumb -->
              <!-- <div class="col-auto">
                <a href="#" class="art-thumb ratio ratio-3x4" aria-label="Open article">
                  <img src="assets/images/issues/issues01.png" alt="" loading="lazy">
                </a>
              </div> -->

              <!-- text -->
              <div class="col overflow-hidden">
                <h3 class="art-title">
                  <a href="#">Domain Shinto as a Testing Ground of Early Modern Shinto</a>
                </h3>
                <ul class="art-meta">
                  <li>Bernhard Scheid</li>
                  <li>2024-12</li>
                  <li>Vol.30</li>
                  <li>pp.17–36</li>
                </ul>
              </div>
            </div>
          </article>

          <!-- Lặp thêm các item tương tự -->
          <article class="art-item" data-aos="fade-up" data-aos-delay="200">
            <div class="row g-3 align-items-start flex-nowrap">
              <div class="col overflow-hidden">
                <h3 class="art-title"><a href="#">Domain Shinto as a Testing Ground of Early Modern Shinto</a></h3>
                <ul class="art-meta">
                  <li>Bernhard Scheid</li>
                  <li>2024-12</li>
                  <li>Vol.30</li>
                  <li>pp.17–36</li>
                </ul>
              </div>
            </div>
          </article>

          <article class="art-item" data-aos="fade-up" data-aos-delay="300">
            <div class="row g-3 align-items-start flex-nowrap">
              <div class="col overflow-hidden">
                <h3 class="art-title"><a href="#">Domain Shinto as a Testing Ground of Early Modern Shinto</a></h3>
                <ul class="art-meta">
                  <li>Bernhard Scheid</li>
                  <li>2024-12</li>
                  <li>Vol.30</li>
                  <li>pp.17–36</li>
                </ul>
              </div>
            </div>
          </article>

          <article class="art-item" data-aos="fade-up" data-aos-delay="100">
            <div class="row g-3 align-items-start flex-nowrap">
              <div class="col overflow-hidden">
                <h3 class="art-title"><a href="#">Domain Shinto as a Testing Ground of Early Modern Shinto</a></h3>
                <ul class="art-meta">
                  <li>Bernhard Scheid</li>
                  <li>2024-12</li>
                  <li>Vol.30</li>
                  <li>pp.17–36</li>
                </ul>
              </div>
            </div>
          </article>

          <article class="art-item" data-aos="fade-up" data-aos-delay="200">
            <div class="row g-3 align-items-start flex-nowrap">
              <div class="col overflow-hidden">
                <h3 class="art-title"><a href="#">Domain Shinto as a Testing Ground of Early Modern Shinto</a></h3>
                <ul class="art-meta">
                  <li>Bernhard Scheid</li>
                  <li>2024-12</li>
                  <li>Vol.30</li>
                  <li>pp.17–36</li>
                </ul>
              </div>
            </div>
          </article>

          <!-- View more -->
          <div class="text-center mt-3" data-aos="fade-up" data-aos-delay="300">
            <a href="#" class="btn btn-viewmore">
              <span>View More</span>
              <span class="btn-circle" aria-hidden="true"><i class="bi bi-arrow-right-short"></i></span>
            </a>
          </div>
        </div>
      </div>

      <!-- Illustrations -->
      <img class="jr-art-ill jr-art-ill-left" src="assets/images/illust_people_left2.png" alt="" aria-hidden="true">
      <img class="jr-art-ill jr-art-ill-right" src="assets/images/illust_people_right2.png" alt="" aria-hidden="true">
    </div>
  </section>

<?php get_template_part('/template-parts/components/contact', 'form'); ?>
<?php get_footer(); ?>