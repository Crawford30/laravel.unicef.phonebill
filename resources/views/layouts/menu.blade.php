<div class="unicef-nav-menu" id="unicef-nav-menu">
    <div class="unicef-menu-header">
        <h3>Menu <button type="button" class="close float-right" @click.prevent="openOrCloseMenu()">&times;</button></h3>
    </div>
    <div class="unicef-menu-content">
        <div class="row unicef-menu-row">
            <div class="col-md">
                <a href="{{ route('help-center') }}">
                    <div class="unicef-menu-item">
                        <div class="unicef-menu-icon">
                            <i class="fa fa-question"></i>
                        </div>
                        <div class="unicef-menu-txt">
                            <h3>Help Center</h3>
                            <p>Review Tutorials &amp; Videos</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md">
                <a href="#">
                    <div class="unicef-menu-item">
                        <div class="unicef-menu-icon">
                            <i class="fa fa-pie-chart"></i>
                        </div>
                        <div class="unicef-menu-txt">
                            <h3>Reports</h3>
                            <p>View/Download Reports</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row unicef-menu-row">
            <div class="col-md">
                <a href="{{ route('logout.platform') }}">
                    <div class="unicef-menu-item">
                        <div class="unicef-menu-icon">
                            <i class="fa fa-lock"></i>
                        </div>
                        <div class="unicef-menu-txt">
                            <h3>Return to Main</h3>
                            <p>Return to main platform</p>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                            </form>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md">
                <a href="#">
                    <div class="unicef-menu-item">
                        <div class="unicef-menu-icon">
                            <i class="fa fa-mobile"></i>
                        </div>
                        <div class="unicef-menu-txt">
                            <h3>Mobile App</h3>
                            <p>Download the App</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
