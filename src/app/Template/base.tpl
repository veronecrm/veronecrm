@section('main')
<?php
    $_route   = $app->get('routing')->getRoute();
    $_module  = $_route->getModule();

    $app->assetter()
        ->load('font-awesome')
        ->load('jquery')
        ->load('bs-css')
        ->load('bs-js')
        ->load('jquery-cookie')
        ->load('app-main-css')
        ->load('app-main-js');
?><!doctype html>
<html class="no-js" lang="pl">
<head>
    <meta name="viewport" content="width=device-width" />
    <meta charset="utf-8" />
    <title>Verone CRM - ver. <?php echo \CRM\Version::VERSION; ?></title>
    <meta name="generator" content="Verone CRM - ver. <?php echo \CRM\Version::VERSION; ?>" />
    <style>body{opacity:0;-moz-transition:all 0.1s ease;-webkit-transition:all 0.1s ease;transition:all 0.1s ease;}</style>
    <!-- Only Fonts are loaded from CDN. -->
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700&amp;subset=latin,greek-ext,greek,vietnamese,latin-ext,cyrillic,cyrillic-ext" />
    <link id="page-favicon" rel="icon" href="<?=$app->request()->getUriForPath('/images/fi.png')?>" />
    <?=$app->assetter()->all('head')?>
    <script src="{{ asset('/assets/app/app.js') }}"></script>
    <script src="{{ asset('/assets/app/lang/'.$app->request()->getLocale().'.js') }}"></script>
    <script>
        APP.system.root = '<?=rtrim($app->request()->getUriForPath('/'), ' /')?>';
        APP.system.assets = APP.system.root + '/assets';
        {# Here is a simple hack to get /index.php path. DO NOT USE asset() FOR THAT! #}
        APP.system.baseUrl = '{{ asset('/index.php') }}';
        APP.system.routing.module = '{{ $_module }}';
        APP.system.routing.controller = '{{ $_route->getController() }}';
        APP.system.routing.action = '{{ $_route->getAction() }}';
        APP.locale = '{{ $app->request()->getLocale() }}';
    </script>
    @show('head.bottom')
</head>
<body>
    <?php
        $sidebarBehavior = $app->openSettings('user')->get('layout.menu.behavior');
    ?>
    @show('body.top')
    <div id="wrapper" class="page-layout-{{ $sidebarBehavior }}">
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".navbar-collapse"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
            <div class="navbar-header">
                <a class="navbar-brand" href="<?=$app->request()->getUriForPath('/')?>">Verone</a>
                <?php echo implode($app->callPlugins('BaseView', 'notificator')); ?>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <?php echo implode($app->callPlugins('BaseView', 'navbarLinks')); ?>
                <li>
                    <a href="#" class="open-action-panel"><i class="fa fa-cogs fa-fw"></i></a>
                </li>
            </ul>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse collapse">
                    <ul class="nav" id="side-menu">
                        <?php
                            $usermenuLinks = [];
                            $base   = [
                                'ordering'  => 0,
                                'icon'      => 'fa fa-asterisk',
                                // @todo Icon as image.
                                //'icon-type' => 'class',
                                'name'      => 'EMPTY',
                                'href'      => $app->createUrl('Home', 'Home'),
                                'module'    => 'Home'
                            ];

                            foreach($app->callPlugins('Links', 'mainMenu') as $module)
                            {
                                if(! is_array($module))
                                {
                                    continue;
                                }

                                foreach($module as $link)
                                {
                                    $usermenuLinks[] = array_merge($base, $link);
                                }
                            }

                            array_multisort($usermenuLinks, SORT_REGULAR);
                        ?>
                        @foreach $usermenuLinks
                            <li<?php echo ($_module == $item['module']) ? ' class="active"' : ''; ?>>
                                <a href="{{ $item['href'] }}"<?php echo ($sidebarBehavior == 2 ? 'data-toggle="tooltip" data-placement="right" title="'.$item['name'].'"' : ''); ?>><i class="{{ $item['icon'] }} fa-fw"></i> {{ $item['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>
        <?php $announcement = $app->get('user.group.manager')->find($app->user()->getGroup())->getAnnouncement(); ?>
        @if $announcement
            <div class="sliding-bar"><div class="slide"><strong>{{ t('alert_exclamanation') }}</strong> {{ $announcement }}</div></div>
        @endif
        <div id="page-wrapper"<?php echo (isset($_COOKIE['page-min-height']) ? ' style="min-height:'.$_COOKIE['page-min-height'].'px"' : ''); ?>>
            <?php
                $alertHeadings = ['info'=>'','success'=>'<i class="fa fa-check"></i> &nbsp;','danger'=>'<i class="fa fa-remove"></i> &nbsp;','warning'=>'<i class="fa fa-exclamation-circle"></i> &nbsp;'];
                foreach(['info'=>'info','success'=>'success','danger'=>'danger','warning'=>'warning'] as $type => $class)
                    foreach($app->flashBag()->get($type, array()) as $flash)
                        echo '<div class="alert alert-'.$class.' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$alertHeadings[$class].$flash.'</div>';
            ?>
            @show('content')
            <div class="footer"><span class="foot-block">2015 &copy; Adam Banaszkiewicz</span> <span class="foot-block"><a href="http://www.veronecrm.com/" target="_blank">www.veronecrm.com</a></span><span class="hidden-xs"> &nbsp; | &nbsp; </span><span class="foot-block"><a href="http://www.veronecrm.com/" target="_blank">Verone</a> &nbsp; <span data-toggle="tooltip" title="{{ t('systemVersion') }}"><?php echo \CRM\Version::VERSION; ?></span></span><span class="hidden-xs"> &nbsp; <!-- <span class="highlighted">(JEST DOSTĘPNA NOWA WERSJA)</span> &nbsp; -->| &nbsp; </span><span class="foot-block"><a href="https://github.com/veronecrm/veronecrm" target="_blank">Open Source Application</a></span><span class="hidden-xs"> &nbsp; | &nbsp; </span><span class="foot-block"><span data-toggle="tooltip" title="{{ t('pageGenerationTime') }}"><?php echo number_format(microtime(true) - $app->request()->getRequestMicrotime(), 2); ?> s.</span></span></div>
        </div>
    </div>
    <div class="ve-panel actions-panel hidden">
        <div class="ve-bl"></div>
        <div class="ve-fl">
            <div class="tops">
                <a href="{{ createUrl('User', 'User', 'selfEdit') }}">
                    <i class="fa fa-user"></i>
                    <span>{{ t('myProfile') }}</span>
                </a>
                <a href="{{ createUrl('Auth', 'Authentication', 'lock') }}">
                    <i class="fa fa-lock"></i>
                    <span>{{ t('lockScreen') }}</span>
                </a>
                <a href="{{ createUrl('Auth', 'Authentication', 'logout') }}" class="danger">
                    <i class="fa fa-power-off"></i>
                    <span>{{ t('signout') }}</span>
                </a>
            </div>
            <div class="bots">
                <a href="{{ createUrl('Settings', 'User') }}" class="default" data-toggle="tooltip" title="{{ t('mySettings') }}">
                    <i class="fa fa-cog"></i>
                </a>
            </div>
            <div class="loader hidden loader-fit-to-container">
                <div class="loader-animate"></div>
            </div>
        </div>
    </div>
    <div class="ve-panel session-expire-panel hidden">
        <div class="ve-bl"></div>
        <div class="ve-fl">
            <h2 class="ve-heading">{{ t('sessionExpired') }}</h2>
            <p>{{ t('yourSessionHasExpiredDueToInactivity') }}</p>
            <a href="<?php echo $app->createUrl('Home', 'Home', 'index'); ?>" class="btn btn-default"><span class="fa fa-lock"></span> {{ t('loginAgain') }}</a>
        </div>
    </div>
    <?=$app->assetter()->all('body')?>
    <!-- Apps (plugins) HTML content -->
    <?php echo implode($app->callPlugins('BaseView', 'bodyEnd')); ?>
    @show('body.bottom')
    <style>body{opacity:1;}</style>
</body>
</html>
@endsection
