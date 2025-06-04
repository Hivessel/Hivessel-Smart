<template>
    <aside
        class="main-sidebar sidebar-dark-secondary elevation-4"
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
                    <!-- <li
                        class="nav-item"
                        
                    >
                        <Link
                            :href="route('client.home')"
                            class="nav-link"
                            role="button"
                            
                        >
                            <i class="fas fa-qrcode pr-2"></i>
                        
                            <p>Home</p>
                        </Link>
                    </li> -->

                    <li
                        class="nav-item"
                        
                    >
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
    
                            <p>
                                Assessment Tool
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.grades.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Grade Level</p>
                                </Link>

                                <!-- <a :href="route('admin.grades.index')" class="nav-link">
                                    <i class="fas fa-qrcode pr-2"></i>
                                
                                    <p>Grade Level</p>
                                </a> -->
                                
                            </li>
    
                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.subjects.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Subject</p>
                                </Link>

                                <!-- <a href="#" class="nav-link">
                                    <i class="fas fa-qrcode pr-2"></i>
                                
                                    <p>Subject</p>
                                </a> -->
                            </li>
    
                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.quarters.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Quarter</p>
                                </Link>
                            </li>

                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.contents.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Contents</p>
                                </Link>
                            </li>
    
                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.proficiency-levels.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Proficiency Level</p>
                                </Link>
                            </li>
    
                            <li
                                class="nav-item"
                                
                            >
                                <Link
                                    :href="route('admin.languages.index')"
                                    class="nav-link"
                                >
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Language</p>
                                </Link>
                            </li>
                        </ul>
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
                        
                            <p>Logout</p>
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