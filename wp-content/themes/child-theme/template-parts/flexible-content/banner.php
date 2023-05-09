<section class="banner">
    <div class="inner">
        <div class="banner__wrapper">
            <div class="object-fit object-fit-cover">
                <?php wld_the( 'image' ); ?>
            </div>
            <?php wld_the( 'title', 'screen-reader-text' ); ?>
            <div class="banner__logo">
                <?php wld_the( 'logo' ); ?>
            </div>
            <ul class="banner__titles">
                <li class="banner__title">
                    <?php wld_the( 'banner-title-1' ); ?>
                </li>
                <li class="banner__title">
                    <?php wld_the( 'banner-title-2' ); ?>
                    </li>
                <li class="banner__title">
                    <?php wld_the( 'banner-title-3' ); ?>
                    </li>
            </ul>
            <p><?php wld_the( 'text' ); ?></p>
            <p>
                <?php while ( wld_wrap( 'link-btn', 'btn')) : ?>
                    <?php wld_the( 'text-on-btn' ); ?>
                <?php endwhile; ?>
            </p>
        </div>
        <ul class="banner__items">
            <li class="banner__item">
                <?php while ( wld_wrap( 'banner-link-1', 'banner__link')) : ?>
                    <?php wld_the( 'banner-image-1' ); ?>
                    <h2 class="banner__sub-title">
                        <?php wld_the( 'banner-sub-title-1' ); ?>
                         <strong>Online Campus</strong></h2>
                    <?php endwhile; ?>
            </li>
            <li class="banner__item">
                <?php while ( wld_wrap( 'banner-link-2', 'banner__link')) : ?>
                    <?php wld_the( 'banner-image-2' ); ?>
                    <h2 class="banner__sub-title">
                        <?php wld_the( 'banner-sub-title-2' ); ?>
                       <strong>Community</strong></h2>
                <?php endwhile; ?>
            </li>
            <li class="banner__item">
                <?php while ( wld_wrap( 'banner-link-3', 'banner__link')) : ?>
                    <?php wld_the( 'banner-image-3' ); ?>
                    <h2 class="banner__sub-title">
                        <?php wld_the( 'banner-sub-title-2' ); ?>
                       <strong>Live Events</strong></h2>
                    <?php endwhile; ?>
            </li>
        </ul>
    </div>
</section>
