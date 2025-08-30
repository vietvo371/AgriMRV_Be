 <aside class="sidebar" id="sidebar">
     <div class="sidebar-header">
         <img src="https://dzfullstack.com/assets/images/logo-img.png" alt="DZ Services">
         <h4>DZ FaceBot</h4>
     </div>
     <ul class="sidebar-menu">
         <li>
             <a  class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                 <i class="fas fa-tachometer-alt"></i>
                 <span>Dashboard</span>
             </a>
         </li>
         <li>
             <a  class="{{ request()->routeIs('admin.sinh-vien.*') ? 'active' : '' }}">
                 <i class="fas fa-broadcast-tower"></i>
                 <span>Quản Lý Sinh Viên</span>
             </a>
         </li>
     </ul>
 </aside>
