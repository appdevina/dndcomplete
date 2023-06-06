 <!-- Main Sidebar Container -->
 <aside class="main-sidebar main-sidebar-custom sidebar-dark-primary elevation-4">
     <!-- Brand Logo -->
     <a href="#" class="brand-link">
         <img src="{{ asset('dnd.png') }}" alt="AdminLTE Logo" class="brand-image" style="opacity: .8">
         <span class="brand-text font-weight-light"><strong>Do and Done</strong></span>
     </a>

     <!-- Sidebar -->
     <div class="sidebar">
         <!-- Sidebar user (optional) -->
         <div class="user-panel mt-3 pb-3 mb-3 d-flex">
             <div class="info">
                 <a href="/dashboard" class="d-block"><strong>{{ auth()->user()->nama_lengkap }}</strong></a>
             </div>
         </div>
         <!-- Sidebar Menu -->
         <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                 <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                 @if (auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href="/user" class="nav-link {{ $active === 'user' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-users"></i>
                             <p>User</p>
                         </a>
                     </li>
                 @endif
                 <li class="nav-item">
                     <a href={{ auth()->user()->role_id == 1 ? '/admin/daily' : '/daily' }}
                         class="nav-link {{ $active === 'daily' ? 'active' : '' }}">
                         <img src="{{ asset('assets') }}/daily.png" width='25' height='25' class='mr-1'>
                         <p>Daily</p>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a href={{ auth()->user()->role_id == 1 ? '/admin/weekly' : '/weekly' }}
                         class="nav-link {{ $active === 'weekly' ? 'active' : '' }}">
                         <img src="{{ asset('assets') }}/week.png" width='25' height='25' class='mr-1'>
                         <p>Weekly</p>
                     </a>
                 </li>
                 @if (auth()->user()->role_id == 1 || (auth()->user()->mn || auth()->user()->mr))
                     <li class="nav-item">
                         <a href={{ auth()->user()->role_id == 1 ? '/admin/monthly' : '/monthly' }}
                             class="nav-link {{ $active === 'monthly' ? 'active' : '' }}">
                             <img src="{{ asset('assets') }}/monthly.png" width='25' height='25' class='mr-1'>
                             <p>Monthly</p>
                         </a>
                     </li>
                 @endif
                 @if (auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href='/admin/report' class="nav-link {{ $active === 'report' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-file-invoice"></i>
                             <p>Report</p>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href='/admin/overopen' class="nav-link {{ $active === 'overopen' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-file-invoice"></i>
                             <p>Cut Point</p>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href="#" class="nav-link {{ $active === 'setting' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-cogs"></i>
                             <p>
                                 Settings
                                 <i class="right fas fa-angle-left"></i>
                             </p>
                         </a>
                         <ul class="nav nav-treeview">
                             <li class="nav-item">
                                 <a href="/setting/role" class="nav-link">
                                     <i class="fas fa-user-cog nav-icon"></i>
                                     <p>Role</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/area" class="nav-link">
                                     <i class="fas fa-map nav-icon"></i>
                                     <p>Area</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/divisi" class="nav-link">
                                     <i class="fas fa-briefcase nav-icon"></i>
                                     <p>Divisi</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/taskcategory" class="nav-link">
                                     <i class="fas fa-list nav-icon"></i>
                                     <p>Task Category</p>
                                 </a>
                             </li>
                             <li class="nav-item">
                                 <a href="/setting/taskstatus" class="nav-link">
                                     <i class="fas fa-list nav-icon"></i>
                                     <p>Task Status</p>
                                 </a>
                             </li>
                         </ul>
                     </li>
                 @else
                     <li class="nav-item">
                         <a href='/result/' class="nav-link {{ $active === 'result' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-poll"></i>
                             <p>Result</p>
                         </a>
                     </li>
                     <li class="nav-item">
                         <a href='/request/' class="nav-link {{ $active === 'request' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-undo"></i>
                             <p>Change Task</p>
                         </a>
                     </li>
                    <li class="nav-item {{ $active === 'teams-daily' ? 'menu-open' : 'menu' }}">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                            My Team
                            <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                            <a href="/teams/daily" class="nav-link {{ $active === 'teams-daily' ? 'active' : '' }}">
                                <img src="{{ asset('assets') }}/daily.png" width='25' height='25' class='mr-1'>
                                <p>Team's Daily</p>
                            </a>
                            </li>
                        </ul>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                            <a href="/teams/weekly" class="nav-link {{ $active === 'teams-weekly' ? 'active' : '' }}">
                                <img src="{{ asset('assets') }}/week.png" width='25' height='25' class='mr-1'>
                                <p>Team's Weekly</p>
                            </a>
                            </li>
                        </ul>
                    </li>
                 @endif
                 @if (auth()->user()->role_id > 2 || auth()->user()->role_id == 1)
                     <li class="nav-item">
                         <a href='/req/' class="nav-link {{ $active === 'req' ? 'active' : '' }}">
                             <i class="nav-icon fas fa-check"></i>
                             <p>Approval</p>
                         </a>
                     </li>
                 @endif
             </ul>
         </nav>
         <!-- /.sidebar-menu -->
     </div>
     <!-- /.sidebar -->

     <div class="sidebar-custom">
         <form action="/logout" method="POST">
             @csrf
             <button type="submit" class="btn btn-link"><i class="fas fa-sign-out-alt"></i> Log Out</button>
         </form>
     </div>
     <!-- /.sidebar-custom -->
 </aside>
