<template>
    <aside
        class="main-sidebar sidebar-dark-secondary elevation-4 text-white"
    >
        <!-- Brand Logo -->
        <!-- <a>
            <img src="https://hivessel.com/wp-content/uploads/2024/03/cropped-hivessel_favicon.png"
                class="brand-image img-circle elevation-3 bg-white" style="opacity: 1; width: 40px;">
        </a> -->
    
        
    
        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul
                    class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview"
                    role="menu"
                    data-accordion="false"
                >

                    <li
                        class="nav-item"
                        
                    >
                        <Link
                            :href="route('client.plugins.assessment-tool')"
                            class="nav-link"
                            role="button"
                        >
                            <i class="fas fa-qrcode pr-2"></i>
                        
                            <p>Assessment Tool</p>
                        </Link>
                    </li>

                    <li
                        class="nav-item"
                        
                    >
                        <Link
                            :href="route('admin.home')"
                            class="nav-link"
                            role="button"
                        >
                            <i class="fas fa-qrcode pr-2"></i>
                        
                            <p>Admin Page</p>
                        </Link>
                    </li>

                    <li
                        class="nav-item"
                        
                    >
                        <Link
                            href="#"
                            class="nav-link"
                            role="button"
                            @click.prevent="logout"
                        >
                            <i class="fas fa-sign-in-alt pr-2"></i>
                        
                            <p>Sign Out</p>
                        </Link>
                    </li>
                    
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <div
            style="
                position: absolute;
                bottom: 10px;
                width: 100%;
                text-align: center;
            "
        >
            <!-- <code class="text-white">v2.5.1</code> -->
        </div>
        <!-- /.sidebar -->
    </aside>
    </template>
    
    <script setup>
  import { useForm, router, Link } from '@inertiajs/vue3';
    

  const form = useForm({});
  const logout = () => {
      form.post(route('logout'), {
          onSuccess: () => {
              // Handle successful logout if needed
            router.visit(route('login'), {
                method: 'get',
                preserveState: false,
                preserveScroll: true,
            });
          },
          onError: (error) => {
              console.error('Logout failed:', error);
          }
      });
    }

    </script>