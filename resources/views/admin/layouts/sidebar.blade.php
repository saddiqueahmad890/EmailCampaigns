 <div class="sidebar" data-background-color="dark">
   <div class="sidebar-logo">
     <!-- Logo Header -->
     <div class="logo-header" data-background-color="dark">
       <a href="/" class="logo">
         <img
           src="/assets/img/kaiadmin/logo_light.png"
           alt="navbar brand"
           class="navbar-brand"
           height="20" />
       </a>
       <div class="nav-toggle">
         <button class="btn btn-toggle toggle-sidebar">
           <i class="gg-menu-right"></i>
         </button>
         <button class="btn btn-toggle sidenav-toggler">
           <i class="gg-menu-left"></i>
         </button>
       </div>
       <button class="topbar-toggler more">
         <i class="gg-more-vertical-alt"></i>
       </button>
     </div>
     <!-- End Logo Header -->
   </div>
   <div class="sidebar-wrapper scrollbar scrollbar-inner">
     <div class="sidebar-content">
       <ul class="nav nav-secondary">
         <li class="nav-section">
           <span class="sidebar-mini-icon">
             <i class="fa fa-ellipsis-h"></i>
           </span>
           <h4 class="text-section">Components</h4>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#formsexclusion">
             <i class="fas fa-pen-square"></i>
             <p>Manage Exclusion</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="formsexclusion">
             <ul class="nav nav-collapse">
               <li>
                 <a href="{{ route('exclusion-list.index') }}">
                   <span class="sub-item">Exclusion List</span>
                 </a>
               </li>


             </ul>
           </div>
         </li>
         </li>
         <li class="nav-item">
           <a data-bs-toggle="collapse" href="#forms">
             <i class="fas fa-pen-square"></i>
             <p>Manage Campaigns</p>
             <span class="caret"></span>
           </a>
           <div class="collapse" id="forms">
             <ul class="nav nav-collapse">
               <li>
                 <a href="{{ url('create-campaign') }}">
                   <span class="sub-item">Create Campaign</span>
                 </a>
               </li>
               <li>
                 <a href="{{ url('campaign') }}">
                   <span class="sub-item">View Campaign</span>
                 </a>
               </li>
               <li>
                 <a href="{{ url('campaigns/send') }}">
                   <span class="sub-item">Send Email</span>
                 </a>
               </li>
             </ul>
           </div>
         </li>
       </ul>
     </div>
   </div>
 </div>