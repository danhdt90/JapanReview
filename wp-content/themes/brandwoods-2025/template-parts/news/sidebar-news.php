<?php 
    $category = $args['category'];
    $tag = $args['tags']; 
?>
<aside class="col-12 col-lg-3">
    <!-- Categories -->
    <div class="news-side mb-5">
        <div class="news-side-title">
            <span class="dot" aria-hidden="true"></span>
            <strong>すべて(Category)</strong>
        </div>
        <ul class="news-side-list" id="news-cats">
            <?php
                if($category) :
                    foreach($category as $index => $cate) {
                        ?>
                            <li><a href="/news?category=<?php echo esc_attr($cate->slug); ?>" data-cat="Announcement"><?= $cate->name; ?></a></li>
                        <?php
                    }
                endif;
            ?>
        </ul>
    </div>

    <!-- Tags -->
    <div class="news-side">
        <div class="news-side-title">
            <span class="dot" aria-hidden="true"></span>
            <strong>すべて(タグ)</strong>
        </div>
        <ul class="news-side-list" id="news-tags">
            <?php
                if($tag) :
                    foreach($tag as $index => $ctag) {
                        ?>
                            <li><a href="/news?tag=<?php echo esc_attr($ctag->slug); ?>" data-cat="Announcement"><?= $ctag->name; ?></a></li>
                        <?php
                    }
                endif;
            ?>
        </ul>
    </div>
</aside>