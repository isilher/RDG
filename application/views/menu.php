    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="{base_url()}">RDG</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="{site_url('home')}">Home</a></li>
              
            </ul>
            <ul class="nav pull-right">
            	{if $user}
            	<li><a href="{site_url('profile')}">Profile</a></li>
				<li><a href="{site_url('auth/logout')}">Logout [ {$user->username} ]</a></li>
				{else}
				<li><a href="{site_url('auth/login')}">Login</a></li>
				<li><a href="{site_url('auth/register')}">Register</a></li>
				{/if}
			</ul>                 
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>