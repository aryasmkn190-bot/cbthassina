 <!--  BEGIN SIDEBAR  -->


 <nav id="sidebar">

     <div class="navbar-nav theme-brand flex-row  text-center">
         <div class="nav-logo">
             <div class="nav-item theme-logo">
                 <a href="<?= base_url('panel/home') ?>">
                     <img src="<?= base_url() ?>assets/img/<?= $setting->logo ?>" class="navbar-logo" alt="logo">
                 </a>
             </div>
             <div class="nav-item theme-text">
                 <a href="<?= base_url('panel/home') ?>" class="nav-link"> <?= $setting->appname ?> </a>
             </div>
         </div>
         <div class="nav-item sidebar-toggle">
             <div class="btn-toggle sidebarCollapse">
                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left">
                     <polyline points="11 17 6 12 11 7"></polyline>
                     <polyline points="18 17 13 12 18 7"></polyline>
                 </svg>
             </div>
         </div>
     </div>
     <div class="profile-info">
         <div class="user-info">
             <div class="profile-img">
                 <img src="<?= base_url() ?>src/assets/img/profile-30.png" alt="avatar">
             </div>
             <div class="profile-content">
                 <h6 class=""><?= session()->get('user')['full_name'] ?></h6>
                 <p class=""><?= session()->get('user')['role'] ?></p>
             </div>
         </div>
     </div>

     <div class="shadow-bottom"></div>
     <ul class="list-unstyled menu-categories" id="accordionExample"></ul>
 </nav>

 <!--  END SIDEBAR  -->

 <?= $this->section('pagejs'); ?>
 <script type="text/javascript">
     // Definisikan array yang berisi data menu dan submenu
     const menuData = [{
             name: 'Dashboard',
             url: 'panel/home',
             icon: 'home',
             roles: ['admin', 'member']
         },
         {
             name: 'Sinkronisasi',
             url: 'panel/sinkronisasi',
             icon: 'refresh-cw',
             roles: ['admin']
         },
         {
             name: 'Data Master',
             icon: 'layers',
             roles: ['admin'],
             submenu: [{
                     name: 'Jenis Ujian',
                     url: 'panel/jenis-ujian',
                     roles: ['admin']
                 },
                 {
                     name: 'Tingkat',
                     url: 'panel/tingkat',
                     roles: ['admin']
                 },
                 {
                     name: 'Kelas',
                     url: 'panel/kelas',
                     roles: ['admin']
                 },
                 {
                     name: 'Jurusan',
                     url: 'panel/jurusan',
                     roles: ['admin']
                 },
                 {
                     name: 'Agama',
                     url: 'panel/agama',
                     roles: ['admin']
                 },
                 {
                     name: 'Ruang',
                     url: 'panel/ruang',
                     roles: ['admin']
                 },
                 {
                     name: 'Sesi',
                     url: 'panel/sesi',
                     roles: ['admin']
                 },

             ]

         },
         {
             name: 'Candy CBT',
             icon: 'command',
             roles: ['admin', 'guru'],
             submenu: [{
                     name: 'Peserta Ujian',
                     url: 'panel/peserta',
                     roles: ['admin']
                 },
                 {
                     name: 'Bank Soal',
                     url: 'panel/banksoal',
                     roles: ['admin', 'guru']
                 },
                 {
                     name: 'Jadwal Ujian',
                     url: 'panel/ujian',
                     roles: ['admin', 'guru']
                 },
                 {
                     name: 'Manajemen Ruang',
                     url: 'panel/ruangsesi',
                     roles: ['admin']
                 },
                 {
                     name: 'Kartu Peserta',
                     url: 'panel/kartu',
                     roles: ['admin']
                 },
                 {
                     name: 'Berita Acara',
                     url: 'panel/beritaacara',
                     roles: ['admin']
                 },
                 {
                     name: 'Analisis Butir Soal',
                     url: 'panel/analisis-butir',
                     roles: ['admin', 'guru']
                 },


             ]

         },
         {
             name: 'QR Generator',
             icon: 'slack',
             roles: ['admin'],
             submenu: [{
                     name: 'Alamat Server',
                     url: 'panel/qrgenerator/server',
                     roles: ['admin']
                 },
                 {
                     name: 'Link Ujian',
                     url: 'panel/qrgenerator/linkujian',
                     roles: ['admin']
                 },
                 {
                     name: 'SEB Config',
                     url: 'panel/qrgenerator/sebconfig',
                     roles: ['admin']
                 },


             ]

         },
         {
             name: 'Exambro',
             icon: 'codesandbox',
             roles: ['admin'],
             submenu: [{
                     name: 'General Setting',
                     url: 'panel/exambro/setting',
                     roles: ['admin']
                 },
                 {
                     name: 'Blokir Aplikasi',
                     url: 'panel/exambro/block',
                     roles: ['admin']
                 },
                 {
                     name: 'Menu Link Aplikasi',
                     url: 'panel/exambro/menu',
                     roles: ['admin']
                 },
                 {
                     name: 'Halaman Informasi',
                     url: 'panel/exambro/informasi',
                     roles: ['admin']
                 },
             ]
         },
         {
             name: 'Manajemen User',
             icon: 'user',
             url: 'panel/users',
             roles: ['admin'],

         },
         {
             name: 'Pengaturan',
             icon: 'settings',
             roles: ['admin'],
             submenu: [{
                     name: 'Identitas Sekolah',
                     url: 'panel/pengaturan/sekolah',
                     roles: ['admin']
                 },
                 {
                     name: 'Patch Updates',
                     url: 'panel/pengaturan/patch',
                     roles: ['admin']
                 },
                 {
                     name: 'Database',
                     url: 'panel/pengaturan/database',
                     roles: ['admin']
                 },
                 //  {
                 //      name: 'Whatsapp Device',
                 //      url: 'pengaturan/whatsapp/device',
                 //      roles: ['admin']
                 //  },
                 //  {
                 //      name: 'Whatsapp Template',
                 //      url: 'pengaturan/whatsapp/template',
                 //      roles: ['admin']
                 //  }
             ]
         }
     ];

     // Fungsi untuk membuat elemen menu
     function createMenuElement(menu) {
         const li = document.createElement('li');
         li.className = 'menu';
         const a = document.createElement('a');
         a.href = menu.submenu ? `#${menu.name.toLowerCase().replace(/\s/g, '')}` : '<?= base_url() ?>' + menu.url || '#';
         a.setAttribute('aria-expanded', 'false');
         a.className = 'dropdown-toggle';
         if (menu.submenu) {
             a.setAttribute('data-bs-toggle', 'collapse');
         }

         const div1 = document.createElement('div');
         div1.innerHTML = `<i data-feather="${menu.icon}"></i><span>${menu.name}</span>`;

         const div2 = document.createElement('div');
         if (menu.submenu) {
             div2.innerHTML = '<i data-feather="chevron-right"></i>';
         }

         a.appendChild(div1);
         a.appendChild(div2);
         li.appendChild(a);

         if (menu.submenu && menu.submenu.length > 0) {
             const ul = document.createElement('ul');
             ul.className = 'collapse submenu list-unstyled';
             ul.id = menu.name.toLowerCase().replace(/\s/g, '');
             ul.setAttribute('data-bs-parent', '#accordionExample');

             menu.submenu.forEach(submenu => {
                 const subLi = document.createElement('li');
                 const subA = document.createElement('a');
                 subA.href = '<?= base_url() ?>' + submenu.url || '#';
                 subA.textContent = submenu.name;
                 subLi.appendChild(subA);
                 ul.appendChild(subLi);
             });

             li.appendChild(ul);
         }

         return li;
     }

     // Tentukan menu yang harus ditampilkan berdasarkan peran pengguna
     function renderMenu(userRole) {
         console.log('User roles:', userRole);
         const menuContainer = document.getElementById('accordionExample');
         menuContainer.innerHTML = ''; // Clear existing menu

         const userRoles = userRoleString.split(',').map(role => role.trim());


         menuData.forEach(menu => {
             const allowedRoles = menu.roles || [];
             const intersection = allowedRoles.filter(role => userRoles.includes(role));
             const hasSubmenu = menu.submenu && menu.submenu.length > 0;
             if (intersection.length > 0) {
                 const menuElement = createMenuElement(menu);
                 menuContainer.appendChild(menuElement);
                 // Check if there are any submenus allowed for the user's role
                 if (hasSubmenu) {
                     const submenuToShow = menu.submenu.filter(submenu => submenu.roles.some(role => userRoles.includes(role)));
                     if (submenuToShow.length > 0) {
                         const submenuContainerId = menu.name.toLowerCase().replace(/\s/g, '');
                         const submenuContainer = document.getElementById(submenuContainerId);
                         submenuContainer.innerHTML = ''; // Clear existing submenu

                         submenuToShow.forEach(submenu => {
                             const subLi = document.createElement('li');
                             const subA = document.createElement('a');
                             subA.href = submenu.url.startsWith('http') ? submenu.url : '<?= base_url() ?>' + submenu.url;
                             subA.textContent = submenu.name;
                             subLi.appendChild(subA);
                             submenuContainer.appendChild(subLi);
                         });
                     }
                 }
             }
         });

     }
     // Misalnya role pengguna adalah 'admin'
     const userRoleString = '<?= session()->get('user')['role'] ?>';
     const userRolesArray = userRoleString.split(',').map(role => role.trim());
     renderMenu(userRolesArray);

     var currentUrl = window.location.href;

     $('#accordionExample').find('a').each(function() {
         var menuUrl = $(this).attr('href');

         if (currentUrl === menuUrl) {
             // Tambahkan kelas aktif pada tautan menu
             $(this).addClass('active');

             // Jika tautan menu adalah submenu, tambahkan kelas active pada dropdown-toggle dan kelas show pada submenu
             if ($(this).hasClass('dropdown-toggle')) {
                 $(this).addClass('active');
                 $(this).next('.submenu').addClass('show');
                 $(this).closest('.menu').addClass('active');
             }

             // Jika tautan menu adalah submenu tetapi tidak memiliki class dropdown-toggle
             if ($(this).closest('.submenu').length > 0) {
                 $(this).closest('.submenu').addClass('show');
                 $(this).closest('.menu').find('.dropdown-toggle').addClass('active');
                 $(this).closest('.menu').addClass('active');
                 $(this).closest('.submenu').closest('.menu').addClass('active'); // Tambahkan class active pada parent li
             }
             // Tambahkan kelas active pada elemen li yang merupakan submenu yang aktif
             $(this).closest('.submenu').find('li').removeClass('active');
             $(this).closest('li').addClass('active');
             // Hentikan iterasi setelah menemukan tautan yang cocok
             return false;
         }

     });
 </script>

 <?= $this->endSection(); ?>