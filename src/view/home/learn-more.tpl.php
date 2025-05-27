<style>
    .learn-more {
        padding: 2rem;
    }

    .learn-more .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .learn-more .category-tabs {
        display: flex;
        gap: 1rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .learn-more .category-tab {
        padding: 1rem 2rem;
        background: var(--white);
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 1.4rem;
        transition: all 0.3s ease;
        border: 1px solid #ddd;
    }

    .learn-more .category-tab.active,
    .learn-more .category-tab:hover {
        background: var(--main-color);
        color: white;
        border-color: var(--main-color);
    }

    .learn-more .search-video {
        margin-bottom: 2rem;
        border-radius: .5rem;
        background-color: var(--white);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .learn-more .search-video input {
        width: 100%;
        background: none;
        font-size: 1.8rem;
        color: var(--black);
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
    }

    .learn-more .search-video input:focus {
        border-color: var(--main-color);
        outline: none;
    }

    .learn-more .search-video button {
        font-size: 2rem;
        color: var(--black);
        cursor: pointer;
        background: none;
        padding: 1rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .learn-more .search-video button:hover {
        color: var(--main-color);
        background: #f8f9fa;
    }

    .learn-more .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(30rem, 1fr));
        gap: 1.5rem;
        justify-content: center;
        align-items: flex-start;
    }

    .learn-more .box-container .box {
        background-color: var(--white);
        border-radius: .5rem;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .learn-more .box-container .box:hover {
        transform: translateY(-5px);
    }

    .learn-more .box-container .box .video,
    .learn-more .box-container .box .tutor {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    .learn-more .box-container .box .video h3,
    .learn-more .box-container .box .tutor h3 {
        font-size: 2rem;
        color: var(--black);
        margin-bottom: .2rem;
    }

    .learn-more .box-container .box .video span,
    .learn-more .box-container .box .tutor span {
        font-size: 1.6rem;
        color: var(--light-color);
    }

    .learn-more .box-container .box p {
        padding: .5rem 0;
        font-size: 1.7rem;
        color: var(--light-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .learn-more .box-container .box p span {
        color: var(--main-color);
        font-weight: bold;
    }

    .learn-more .box-container .box .video-info {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .learn-more .box-container .box .video-info .duration {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--light-color);
        font-size: 1.4rem;
    }

    .learn-more .box-container .box a {
        display: inline-block;
        background-color: var(--main-color);
        border-radius: .5rem;
        padding: 1rem 1.5rem;
        font-size: 1.8rem;
        color: var(--white);
        text-align: center;
        margin-top: 1rem;
        text-transform: capitalize;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .learn-more .box-container .box a:hover {
        background-color: #004408;
        transform: translateY(-2px);
    }

    .learn-more .featured-section {
        margin-bottom: 3rem;
    }

    .learn-more .featured-section h2 {
        font-size: 2rem;
        color: var(--black);
        margin-bottom: 1.5rem;
    }
</style>

<section class="learn-more">
    <div class="page-header">
        <h1 class="heading">Video Tutorials</h1>
        <div class="search-video">
            <input type="text" name="search_box" placeholder="Search tutorials..." required maxlength="100">
            <button type="submit" class="fas fa-search" name="search_tutor"></button>
        </div>
    </div>

    <div class="category-tabs">
        <div class="category-tab active">All Tutorials</div>
        <div class="category-tab">Property Sales</div>
        <div class="category-tab">E-Government</div>
        <div class="category-tab">Documentation</div>
        <div class="category-tab">FAQs</div>
    </div>

    <div class="featured-section">
        <h2>Featured Tutorials</h2>
        <div class="box-container">
            <div class="box">
                <div class="video">
                    <div>
                        <h3>How to apply for E-sell property</h3>
                        <span>Step-by-step guide</span>
                    </div>
                </div>
                <p><i class="fas fa-eye"></i> Total views: <span>1,208</span></p>
                <div class="video-info">
                    <div class="duration">
                        <i class="far fa-clock"></i> 15:30
                    </div>
                    <div class="duration">
                        <i class="fas fa-calendar"></i> Updated: 2 days ago
                    </div>
                </div>
                <a href="watch-video" class="inline-btn">Watch Tutorial</a>
            </div>

            <div class="box">
                <div class="tutor">
                    <div>
                        <h3>E-government Services</h3>
                        <span>Complete overview</span>
                    </div>
                </div>
                <p><i class="fas fa-eye"></i> Total views: <span>1,088</span></p>
                <div class="video-info">
                    <div class="duration">
                        <i class="far fa-clock"></i> 12:45
                    </div>
                    <div class="duration">
                        <i class="fas fa-calendar"></i> Updated: 1 week ago
                    </div>
                </div>
                <a href="watch-video2" class="inline-btn">Watch Tutorial</a>
            </div>
        </div>
    </div>

    <div class="featured-section">
        <h2>Popular Tutorials</h2>
        <div class="box-container">
            <div class="box">
                <div class="video">
                    <div>
                        <h3>Property Documentation Guide</h3>
                        <span>Required documents</span>
                    </div>
                </div>
                <p><i class="fas fa-eye"></i> Total views: <span>956</span></p>
                <div class="video-info">
                    <div class="duration">
                        <i class="far fa-clock"></i> 18:20
                    </div>
                    <div class="duration">
                        <i class="fas fa-calendar"></i> Updated: 3 days ago
                    </div>
                </div>
                <a href="watch-video3" class="inline-btn">Watch Tutorial</a>
            </div>

            <div class="box">
                <div class="tutor">
                    <div>
                        <h3>Digital Signature Process</h3>
                        <span>Security guide</span>
                    </div>
                </div>
                <p><i class="fas fa-eye"></i> Total views: <span>845</span></p>
                <div class="video-info">
                    <div class="duration">
                        <i class="far fa-clock"></i> 10:15
                    </div>
                    <div class="duration">
                        <i class="fas fa-calendar"></i> Updated: 5 days ago
                    </div>
                </div>
                <a href="watch-video4" class="inline-btn">Watch Tutorial</a>
            </div>
        </div>
    </div>
</section>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
