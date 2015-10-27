<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Company Hiring - Super Long Job Title">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Company Hiring - Super Long Job Title</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="GiveToken">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="/images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <!-- Favicon -->
  	<link rel="apple-touch-icon" sizes="57x57" href="/assets/gt-favicons.ico/apple-icon-57x57.png">
  	<link rel="apple-touch-icon" sizes="60x60" href="/assets/gt-favicons.ico/apple-icon-60x60.png">
  	<link rel="apple-touch-icon" sizes="72x72" href="/assets/gt-favicons.ico/apple-icon-72x72.png">
  	<link rel="apple-touch-icon" sizes="76x76" href="/assets/gt-favicons.ico/apple-icon-76x76.png">
  	<link rel="apple-touch-icon" sizes="114x114" href="/assets/gt-favicons.ico/apple-icon-114x114.png">
  	<link rel="apple-touch-icon" sizes="120x120" href="/assets/gt-favicons.ico/apple-icon-120x120.png">
  	<link rel="apple-touch-icon" sizes="144x144" href="/assets/gt-favicons.ico/apple-icon-144x144.png">
  	<link rel="apple-touch-icon" sizes="152x152" href="/assets/gt-favicons.ico/apple-icon-152x152.png">
  	<link rel="apple-touch-icon" sizes="180x180" href="/assets/gt-favicons.ico/apple-icon-180x180.png">
  	<link rel="icon" type="image/png" sizes="192x192"  href="/assets/gt-favicons.ico/android-icon-192x192.png">
  	<link rel="icon" type="image/png" sizes="32x32" href="/assets/gt-favicons.ico/favicon-32x32.png">
  	<link rel="icon" type="image/png" sizes="96x96" href="/assets/gt-favicons.ico/favicon-96x96.png">
  	<link rel="icon" type="image/png" sizes="16x16" href="/assets/gt-favicons.ico/favicon-16x16.png">
  	<link rel="manifest" href="/assets/gt-favicons.ico/manifest.json">
  	<meta name="msapplication-TileColor" content="#ffffff">
  	<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
  	<meta name="theme-color" content="#ffffff">
  	<!-- endFavicon -->

    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!--
    <link rel="canonical" href="http://www.example.com/">
    -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://storage.googleapis.com/code.getmdl.io/1.0.5/material.blue-green.min.css">
    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/recruiting-token.css">

    <!-- jQuery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <!-- Polymer -->
    <script src="/bower_components/webcomponentsjs/webcomponents-lite.min.js"></script>
    <link rel="import" href="/bower_components/google-map/google-map.html">
    <link rel="import" href="/bower_components/paper-styles/paper-styles.html">
    <link rel="import" href="/bower_components/neon-animation/neon-animated-pages.html">
    <link rel="import" href="/bower_components/neon-animation/neon-animations.html">
    <link rel="import" href="/elements/description-x-card.html">
    <link rel="import" href="/elements/image-x-card.html">
    <link rel="import" href="/elements/location-x-card.html">
    <link rel="import" href="/elements/video-x-card.html">
    <link rel="import" href="/elements/x-card.html">
    <link rel="import" href="/elements/x-cards-list.html">

  </head>
  <body class="mdl-demo mdl-color--grey-100 mdl-color-text--grey-700 mdl-base fullbleed">


    <template is="dom-bind">
      <neon-animated-pages id="pages" selected="0">
          <x-cards-list id="list">
            <div class="fit layout vertical center-center">
              <div class="fit layout horizontal large">

                <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
                  <header class="mdl-layout__header mdl-layout__header--waterfall">
                    <!-- Top row, always visible -->
                    <div class="mdl-layout__header-row">
                      <span class="mdl-layout-title long-title">
                        Company Hiring <i class="current-section"></i>
                      </span>
                      <div class="mdl-layout-spacer"></div>
                    </div>
                    <!-- Bottom rows, not visible on scroll -->
                    <div class="mdl-layout__header-row">
                      <div class="mdl-layout-spacer"></div>
                    </div>
                    <div class="mdl-layout__header-row">
                      <span class="mdl-layout-title">
                        <h1>Super Long Job Title</h1>
                      </span>
                      <div class="mdl-layout-spacer"></div>
                    </div>
                    <div class="mdl-layout__header-row">
                      <div class="mdl-layout-spacer"></div>
                    </div>
                  </header>
                  <div class="mdl-layout__drawer" id="menuDrawer">
                    <!--<span class="mdl-layout-title">Job Description</span>-->
                    <nav class="mdl-navigation">
                      <a class="mdl-layout-title mdl-navigation__link">Job Description</a>
                      <a id="overview-drawer" class="mdl-navigation__link" href="#overview-section" on-click="_onOverviewClick">Overview</a>
                      <a id="skills-drawer"  class="mdl-navigation__link" href="#skills-section" on-click="_onSkillsClick">Skills Required</a>
                      <a id="responsibilities-drawer"  class="mdl-navigation__link" href="#responsibilities-section" on-click="_onResponsibilitiesClick">Responsibilities</a>
                      <a id="values-drawer"  class="mdl-navigation__link" href="#values-section" on-click="_onValuesClick">Values</a>
                      <a id="perks-drawer"  class="mdl-navigation__link" href="#perks-section" on-click="_onPerksClick">Perks</a>
                      <a id="location-drawer" class="mdl-layout-title mdl-navigation__link" href="#location-section" on-click="_onLocationClick">Location</a>
                      <a id="images-drawer" class="mdl-layout-title mdl-navigation__link" href="#images-section" on-click="_onImagesClick">Images</a>
                      <a id="videos-drawer" class="mdl-layout-title mdl-navigation__link" href="#videos-section" on-click="_onVideosClick">Videos</a>
                    </nav>
                  </div>
                  <main class="mdl-layout__content" on-scroll="_onTrack">
                    <div class="mdl-layout__tab-panel is-active" id="overview">

                      <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
                        <div class="mdl-card mdl-cell mdl-cell--4-col" id="job-description-icon">
                          <div class="mdl-card__supporting-icon">
                            <h4 class="mdl-color-text--primary-contrast"><i class="material-icons huge-icon">work</i></h4>
                          </div>
                        </div>
                        <div class="mdl-card mdl-cell mdl-cell--8-col">
                          <div class="mdl-card__supporting-text">
                            <h4 class="mdl-color-text--primary-dark">Job Description</h4>
                            <p>
                              Dolore ex deserunt aute fugiat aute nulla ea sunt aliqua nisi cupidatat eu. Nostrud in laboris labore nisi amet do dolor eu fugiat consectetur elit cillum esse. Pariatur occaecat nisi laboris tempor laboris eiusmod qui id Lorem esse commodo in. Exercitation aute dolore deserunt culpa consequat elit labore incididunt elit anim.
                            </p>
                          </div>
                          <div class="mdl-row">
                            <a href="#overview-section" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect job-description-option overview" on-click="_onOverviewClick">
                              Overview
                            </a>
                            <a href="#skills-section" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect job-description-option skills" on-click="_onSkillsClick">
                              Skills Required
                            </a>
                            <a href="#responsibilities-section" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect job-description-option responsibilities" on-click="_onResponsibilitiesClick">
                              Responsibilities
                            </a>
                            <a href="#values-section" class="mdl-cell--2-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect job-description-option values" on-click="_onValuesClick">
                              Values
                            </a>
                            <a href="#perks-section" class="mdl-cell--2-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect job-description-option perks" on-click="_onPerksClick">
                              Perks
                            </a>
                          </div>
                        </div>
                      </section>
                      <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp social-section">
                        <a href="http://twitter.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-twitter big-icon"></i>
                        </a>
                        <a href="http://facebook.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-facebook big-icon"></i>
                        </a>
                        <a href="http://linkedin.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-linkedin big-icon"></i>
                        </a>
                        <a href="http://youtube.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-youtube big-icon"></i>
                        </a>
                        <a href="http://plus.google.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-google-plus big-icon"></i>
                        </a>
                        <a href="http://pinterest.com" target="_blank" class="mdl-cell--2-col mdl-button--raised mdl-js-ripple-effect mdl-color-text--primary-contrast frontpage-social-button">
                          <i class="fa fa-pinterest big-icon"></i>
                        </a>
                      </section>
                      <section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp" on-click="_onLocationClick">
                        <div class="mdl-card mdl-cell mdl-cell--12-col" id="location-frontpage">
                          <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
                            <h2 class="mdl-cell mdl-cell--12-col">
                              <i class="material-icons huge-icon">room</i>
                              <br />
                              Location
                            </h2>
                          </div>
                        </div>
                      </section>
                      <section class="section--center mdl-grid">
                        <div class="mdl-card mdl-cell mdl-cell--6-col mdl-shadow--2dp" id="images-frontpage" on-click="_onImagesClick">
                          <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
                            <h2 class="mdl-cell mdl-cell--12-col">
                              <i class="material-icons huge-icon">image</i>
                              <br />
                              Images
                            </h2>
                          </div>
                        </div>
                        <div class="mdl-card mdl-cell mdl-cell--6-col mdl-shadow--2dp" id="videos-frontpage" on-click="_onVideosClick">
                          <div class="mdl-card__supporting-text mdl-grid mdl-grid--no-spacing">
                            <h2 class="mdl-cell mdl-cell--12-col">
                              <i class="material-icons huge-icon">videocam</i>
                              <br />
                              Videos
                            </h2>
                          </div>
                        </div>
                      </section>
                      <section class="section--footer mdl-color--light-grey mdl-grid">
                      </section>
                    </div>
                  </main>
                </div>
              </div>
            </div>
            <div id="interested-disabled-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-color--primary-dark" disabled>Interested?</div>
            <div id="interested-yes-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onYesClick">
              YES
            </div>
            <div id="interested-maybe-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onMaybeClick">
              MAYBE
            </div>
            <div id="interested-no-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onNoClick">
              NO
            </div>
          </x-cards-list>
          <x-card>
            <div class="fit layout vertical center-center">
              <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
                BACK
              </div>
              <h2 class="mdl-color-text--primary-dark">Yes</h2>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
            </div>
          </x-card>
          <x-card>
            <div class="fit layout vertical center-center">
              <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
                BACK
              </div>
              <h2 class="mdl-color-text--primary-dark">Maybe, I'm indecisive</h2>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
            </div>
          </x-card>
          <x-card>
            <div class="fit layout vertical center-center">
              <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
                BACK
              </div>
              <h2 class="mdl-color-text--primary-dark">No No No!</h2>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
              </p>
            </div>
          </x-card>
          <location-x-card>
            <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
              BACK
            </div>
            <div id="interested-disabled-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-color--primary-dark" disabled>Interested?</div>
            <div id="interested-yes-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onYesClick">
              YES
            </div>
            <div id="interested-maybe-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onMaybeClick">
              MAYBE
            </div>
            <div id="interested-no-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onNoClick">
              NO
            </div>
          </location-x-card>
          <image-x-card>
            <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
              BACK
            </div>
            <div id="interested-disabled-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-color--primary-dark" disabled>Interested?</div>
            <div id="interested-yes-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onYesClick">
              YES
            </div>
            <div id="interested-maybe-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onMaybeClick">
              MAYBE
            </div>
            <div id="interested-no-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onNoClick">
              NO
            </div>
          </image-x-card>
          <video-x-card>
            <div class="mdl-button mdl-js-button mdl-button--raised back-button" on-click="_onBackClick">
              BACK
            </div>
            <div id="interested-disabled-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-color--primary-dark" disabled>Interested?</div>
            <div id="interested-yes-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onYesClick">
              YES
            </div>
            <div id="interested-maybe-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onMaybeClick">
              MAYBE
            </div>
            <div id="interested-no-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onNoClick">
              NO
            </div>
          </video-x-card>
          <description-x-card>
            <div id="interested-disabled-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-color--primary-dark" disabled>Interested?</div>
            <div id="interested-yes-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onYesClick">
              YES
            </div>
            <div id="interested-maybe-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onMaybeClick">
              MAYBE
            </div>
            <div id="interested-no-button" class="mdl-cell--3-col mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" on-click="_onNoClick">
              NO
            </div>
          </description-x-card>
      </neon-animated-pages>
    </template>

    <!-- JavaScript -->
    <script src="https://storage.googleapis.com/code.getmdl.io/1.0.5/material.min.js"></script>
    <script src="/js/recruiting-token.js"></script>
  </body>
</html>
