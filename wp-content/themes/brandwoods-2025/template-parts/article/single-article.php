<?php
/**
 * The template for displaying single article posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BrandWoods_2025
 * @since BrandWoods 2025 1.0
 */

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <article class="single-article">
            <!-- Article Header -->
            <header class="article-header">
                <h1 class="article-title"><?php the_title(); ?></h1>
                
                <!-- Main Titles (additional titles) -->
                <?php 
                $main_titles = pods_field('main_title');
                if (!empty($main_titles) && is_array($main_titles)): ?>
                    <div class="additional-titles">
                        <?php foreach ($main_titles as $index => $title): ?>
                            <h2 class="additional-title">タイトル[<?php echo ($index + 1); ?>]: <?php echo esc_html($title); ?></h2>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Other Titles -->
                <?php 
                $other_titles = pods_field('other_title');
                if (!empty($other_titles) && is_array($other_titles)): ?>
                    <div class="other-titles">
                        <h3>その他のタイトル:</h3>
                        <?php foreach ($other_titles as $index => $title): ?>
                            <p class="other-title">その他のタイトル[<?php echo $index; ?>]: <?php echo esc_html($title); ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </header>

            <!-- Article Meta Information -->
            <div class="article-meta">
                <div class="meta-grid">
                    <!-- Authors -->
                    <?php 
                    $group_authors = pods_field('group_author');
                    if (!empty($group_authors) && is_array($group_authors)): ?>
                        <div class="meta-item authors">
                            <span class="meta-label">著者:</span>
                            <div class="meta-value">
                                <?php foreach ($group_authors as $index => $author): ?>
                                    <span class="author">著者[<?php echo $index; ?>]: <?php echo esc_html($author); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Resource Type -->
                    <?php 
                    $resource_type = pods_field('resource_type');
                    if (!empty($resource_type)): ?>
                        <div class="meta-item resource-type">
                            <span class="meta-label">資源タイプ:</span>
                            <span class="meta-value">
                                <?php 
                                if (is_array($resource_type)) {
                                    echo esc_html(is_array($resource_type[0]) ? $resource_type[0] : implode(', ', $resource_type));
                                } else {
                                    echo esc_html($resource_type);
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- DOI -->
                    <?php 
                    $doi = pods_field('doi');
                    if (!empty($doi)): ?>
                        <div class="meta-item doi">
                            <span class="meta-label">ID登録 (DOI):</span>
                            <span class="meta-value">
                                <?php 
                                if (is_array($doi)) {
                                    echo esc_html(is_array($doi[0]) ? $doi[0] : implode(', ', $doi));
                                } else {
                                    echo esc_html($doi);
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <!-- Publication Info -->
                    <div class="publication-info">
                        <?php 
                        $volume = pods_field('volume');
                        $publication_date = pods_field('publication_date');
                        $start_page = pods_field('start_page');
                        $end_page = pods_field('end_page');
                        
                        // Helper function to extract value from array or string
                        $extract_value = function($field) {
                            if (is_array($field)) {
                                return !empty($field[0]) ? $field[0] : '';
                            }
                            return $field;
                        };
                        
                        $volume_value = $extract_value($volume);
                        $date_value = $extract_value($publication_date);
                        $start_page_value = $extract_value($start_page);
                        $end_page_value = $extract_value($end_page);
                        
                        if (!empty($volume_value) || !empty($date_value) || !empty($start_page_value) || !empty($end_page_value)): ?>
                            <div class="meta-item publication">
                                <span class="meta-label">書誌情報:</span>
                                <div class="meta-value publication-details">
                                    <?php if (!empty($volume_value)): ?>
                                        <span class="volume">巻: <?php echo esc_html($volume_value); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($date_value)): ?>
                                        <span class="pub-date">発行日: <?php echo esc_html($date_value); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($start_page_value) || !empty($end_page_value)): ?>
                                        <span class="pages">
                                            ページ: 
                                            <?php if (!empty($start_page_value)): echo esc_html($start_page_value); endif; ?>
                                            <?php if (!empty($start_page_value) && !empty($end_page_value)): echo ' - '; endif; ?>
                                            <?php if (!empty($end_page_value)): echo esc_html($end_page_value); endif; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Article Content -->
            <div class="article-content">
                <!-- Content Description -->
                <?php 
                $content_descriptions = pods_field('content_description');
                if (!empty($content_descriptions) && is_array($content_descriptions)): ?>
                    <div class="content-descriptions">
                        <h3>内容記述:</h3>
                        <?php foreach ($content_descriptions as $index => $description): ?>
                            <div class="content-description">
                                <strong>内容記述[<?php echo $index; ?>]:</strong>
                                <p><?php echo wp_kses_post($description); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Abstract -->
                <?php 
                $abstracts = pods_field('abstract');
                if (!empty($abstracts) && is_array($abstracts)): ?>
                    <div class="abstracts">
                        <h3>抄録:</h3>
                        <?php foreach ($abstracts as $index => $abstract): ?>
                            <div class="abstract">
                                <strong>抄録[<?php echo $index; ?>]:</strong>
                                <p><?php echo wp_kses_post($abstract); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- WordPress Content -->
                <?php if (get_the_content()): ?>
                    <div class="wp-content">
                        <h3>本文:</h3>
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- External Links -->
            <div class="external-links">
                <?php 
                $external_link_title = pods_field('external_link_title');
                $external_link = pods_field('external_link');
                
                // Extract values if they're arrays
                $title_value = is_array($external_link_title) ? (!empty($external_link_title[0]) ? $external_link_title[0] : '') : $external_link_title;
                $link_value = is_array($external_link) ? (!empty($external_link[0]) ? $external_link[0] : '') : $external_link;
                
                if (!empty($title_value) || !empty($link_value)): ?>
                    <div class="external-link-section">
                        <h3>外部リンク:</h3>
                        <?php if (!empty($title_value)): ?>
                            <p class="link-title"><?php echo esc_html($title_value); ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($link_value)): ?>
                            <a href="<?php echo esc_url($link_value); ?>" target="_blank" rel="noopener noreferrer" class="external-link-url">
                                <?php echo esc_html($link_value); ?> <span class="external-icon">↗</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cover Image -->
            <?php 
            $cover_image = pods_field('cover_image');
            if (!empty($cover_image)): ?>
                <div class="cover-image">
                    <h3>表紙:</h3>
                    <div class="image-container">
                        <?php if (is_array($cover_image)): ?>
                            <img src="<?php echo esc_url($cover_image['guid']); ?>" alt="<?php echo esc_attr($cover_image['post_title']); ?>" />
                        <?php else: ?>
                            <img src="<?php echo esc_url($cover_image); ?>" alt="Cover Image" />
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </article>
        
    </main>
</div>

<style>
/* Single Article Styles */
.single-article {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Article Header */
.article-header {
    border-bottom: 3px solid #2563eb;
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
}

.article-title {
    font-size: 2rem;
    color: #1f2937;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.additional-titles {
    margin-top: 1rem;
}

.additional-title {
    font-size: 1.3rem;
    color: #4b5563;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.other-titles {
    margin-top: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 6px;
    border-left: 4px solid #60a5fa;
}

.other-titles h3 {
    margin-bottom: 0.75rem;
    color: #374151;
    font-size: 1.1rem;
}

.other-title {
    margin: 0.5rem 0;
    color: #6b7280;
}

/* Article Meta */
.article-meta {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.meta-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.meta-item {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.meta-label {
    font-weight: 600;
    color: #374151;
    min-width: 120px;
}

.meta-value {
    color: #6b7280;
    flex: 1;
}

.authors .meta-value {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.author {
    padding: 0.25rem 0.5rem;
    background: #dbeafe;
    border-radius: 4px;
    font-size: 0.9rem;
}

.publication-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.publication-details span {
    padding: 0.25rem 0;
}

/* Article Content */
.article-content {
    margin-bottom: 2rem;
}

.content-descriptions,
.abstracts {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fefefe;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.content-descriptions h3,
.abstracts h3 {
    margin-bottom: 1rem;
    color: #1f2937;
    font-size: 1.25rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.content-description,
.abstract {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 6px;
    border-left: 4px solid #10b981;
}

.content-description:last-child,
.abstract:last-child {
    margin-bottom: 0;
}

.content-description strong,
.abstract strong {
    color: #059669;
    display: block;
    margin-bottom: 0.5rem;
}

.wp-content {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.wp-content h3 {
    margin-bottom: 1rem;
    color: #1f2937;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

/* External Links */
.external-links {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fffbeb;
    border: 1px solid #fbbf24;
    border-radius: 8px;
}

.external-link-section h3 {
    margin-bottom: 1rem;
    color: #92400e;
}

.link-title {
    font-weight: 600;
    color: #78350f;
    margin-bottom: 0.5rem;
}

.external-link-url {
    color: #1d4ed8;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    background: #dbeafe;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.external-link-url:hover {
    background: #bfdbfe;
    text-decoration: underline;
}

.external-icon {
    font-size: 0.9rem;
}

/* Cover Image */
.cover-image {
    margin-bottom: 2rem;
    text-align: center;
}

.cover-image h3 {
    margin-bottom: 1rem;
    color: #1f2937;
}

.image-container {
    max-width: 400px;
    margin: 0 auto;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-container img {
    width: 100%;
    height: auto;
    display: block;
}

/* Responsive Design */
@media (max-width: 768px) {
    .single-article {
        padding: 1rem;
        margin: 1rem;
    }
    
    .article-title {
        font-size: 1.5rem;
    }
    
    .meta-item {
        flex-direction: column;
    }
    
    .meta-label {
        min-width: auto;
        font-size: 0.9rem;
    }
    
    .publication-details {
        font-size: 0.9rem;
    }
}
</style>