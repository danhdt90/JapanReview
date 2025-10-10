<?php
/**
 * The template for displaying book archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#archive
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <!-- Archive Header -->
        <header class="page-header books-archive-header">
            <h1 class="page-title"><?php _e( 'All Books', 'twentytwentyfive' ); ?></h1>
            <?php if ( get_post_type_object( 'book' )->description ) : ?>
                <div class="archive-description">
                    <?php echo esc_html( get_post_type_object( 'book' )->description ); ?>
                </div>
            <?php endif; ?>
        </header>

        <!-- Genre Filter -->
        <?php
        $genres = get_terms( array(
            'taxonomy' => 'book_genre',
            'hide_empty' => true,
        ) );
        
        if ( $genres && ! is_wp_error( $genres ) ) : ?>
            <div class="book-genre-filter">
                <h3><?php _e( 'Filter by Genre:', 'twentytwentyfive' ); ?></h3>
                <div class="genre-links">
                    <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" 
                       class="genre-filter-link <?php echo ! is_tax() ? 'active' : ''; ?>">
                        <?php _e( 'All Books', 'twentytwentyfive' ); ?>
                    </a>
                    <?php foreach ( $genres as $genre ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $genre ) ); ?>" 
                           class="genre-filter-link <?php echo is_tax( 'book_genre', $genre->slug ) ? 'active' : ''; ?>">
                            <?php echo esc_html( $genre->name ); ?>
                            <span class="book-count">(<?php echo $genre->count; ?>)</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( have_posts() ) : ?>

            <!-- Books Grid -->
            <div class="books-grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'book-card' ); ?>>
                        <div class="book-card-inner">
                            
                            <!-- Book Cover -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="book-card-cover">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail( 'medium', array( 'alt' => get_the_title() ) ); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Book Details -->
                            <div class="book-card-content">
                                <h2 class="book-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                
                                <!-- Book Meta -->
                                <div class="book-card-meta">
                                    <!-- Author -->
                                    <div class="book-card-author">
                                        <?php _e( 'by', 'twentytwentyfive' ); ?> 
                                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
                                            <?php the_author(); ?>
                                        </a>
                                    </div>
                                    
                                    <!-- Genres -->
                                    <?php
                                    $post_genres = get_the_terms( get_the_ID(), 'book_genre' );
                                    if ( $post_genres && ! is_wp_error( $post_genres ) ) : ?>
                                        <div class="book-card-genres">
                                            <?php foreach ( $post_genres as $genre ) : ?>
                                                <a href="<?php echo esc_url( get_term_link( $genre ) ); ?>" 
                                                   class="book-genre-tag">
                                                    <?php echo esc_html( $genre->name ); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Price -->
                                    <?php $price = get_post_meta( get_the_ID(), 'price', true ); ?>
                                    <?php if ( $price ) : ?>
                                        <div class="book-card-price">
                                            <?php echo esc_html( $price ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Excerpt -->
                                <?php if ( has_excerpt() ) : ?>
                                    <div class="book-card-excerpt">
                                        <?php the_excerpt(); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Read More -->
                                <div class="book-card-footer">
                                    <a href="<?php the_permalink(); ?>" class="read-more-btn">
                                        <?php _e( 'View Details', 'twentytwentyfive' ); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="books-pagination">
                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => __( '← Previous', 'twentytwentyfive' ),
                    'next_text' => __( 'Next →', 'twentytwentyfive' ),
                ) );
                ?>
            </div>

        <?php else : ?>

            <!-- No Books Found -->
            <section class="no-books-found">
                <h2><?php _e( 'No Books Found', 'twentytwentyfive' ); ?></h2>
                <p><?php _e( 'Sorry, no books were found matching your criteria.', 'twentytwentyfive' ); ?></p>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'book' ) ); ?>" class="back-link">
                    <?php _e( 'View All Books', 'twentytwentyfive' ); ?>
                </a>
            </section>

        <?php endif; ?>

    </main>
</div>

<!-- Custom CSS for Books Archive -->
<style>
.books-archive-header {
    text-align: center;
    margin-bottom: 3rem;
    padding: 2rem 0;
    border-bottom: 1px solid #eee;
}

.books-archive-header .page-title {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    color: #1a1a1a;
}

.archive-description {
    font-size: 1.1rem;
    color: #666;
}

.book-genre-filter {
    margin-bottom: 3rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.book-genre-filter h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: #333;
}

.genre-links {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.genre-filter-link {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: 20px;
    text-decoration: none;
    color: #333;
    font-size: 0.9rem;
    transition: all 0.3s;
}

.genre-filter-link:hover,
.genre-filter-link.active {
    background: #c41e3a;
    color: white;
    border-color: #c41e3a;
}

.book-count {
    font-size: 0.8rem;
    opacity: 0.8;
}

.books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.book-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.book-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.book-card-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.book-card-cover {
    flex: 0 0 auto;
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
}

.book-card-cover img {
    width: auto;
    height: 200px;
    object-fit: cover;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.book-card-content {
    flex: 1;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}

.book-card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.book-card-title a {
    color: #1a1a1a;
    text-decoration: none;
}

.book-card-title a:hover {
    color: #c41e3a;
}

.book-card-meta {
    margin-bottom: 1rem;
}

.book-card-author {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.book-card-author a {
    color: #333;
    text-decoration: none;
}

.book-card-author a:hover {
    color: #c41e3a;
}

.book-card-genres {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
}

.book-genre-tag {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
    background: #f0f0f0;
    border-radius: 12px;
    text-decoration: none;
    color: #333;
    transition: background-color 0.3s;
}

.book-genre-tag:hover {
    background: #e0e0e0;
}

.book-card-price {
    font-weight: bold;
    color: #c41e3a;
    font-size: 1.1rem;
}

.book-card-excerpt {
    flex: 1;
    font-size: 0.9rem;
    line-height: 1.5;
    color: #666;
    margin-bottom: 1rem;
}

.book-card-footer {
    margin-top: auto;
}

.read-more-btn {
    display: inline-block;
    width: 100%;
    padding: 0.75rem;
    background: #c41e3a;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 4px;
    font-weight: 600;
    transition: background-color 0.3s;
}

.read-more-btn:hover {
    background: #a01729;
}

.books-pagination {
    text-align: center;
    margin: 3rem 0;
}

.no-books-found {
    text-align: center;
    padding: 3rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.no-books-found h2 {
    color: #333;
    margin-bottom: 1rem;
}

.no-books-found p {
    color: #666;
    margin-bottom: 2rem;
}

.back-link {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #c41e3a;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.back-link:hover {
    background: #a01729;
}

/* Responsive Design */
@media (max-width: 768px) {
    .books-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .book-card-cover img {
        height: 150px;
    }
    
    .genre-links {
        flex-direction: column;
        align-items: stretch;
    }
    
    .genre-filter-link {
        justify-content: space-between;
        border-radius: 4px;
    }
}

@media (max-width: 480px) {
    .books-archive-header .page-title {
        font-size: 2rem;
    }
    
    .book-genre-filter {
        padding: 1rem;
    }
}
</style>

<?php get_footer(); ?>