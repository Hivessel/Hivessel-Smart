<template>
    <div>
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        
        <ul class="navbar-nav">
            <li class="nav-item">
                <a
                    class="nav-link"
                    data-widget="pushmenu"
                    href="#"
                    role="button"
                    ><i class="fas fa-bars"></i
                ></a>
            </li>
            
        </ul>
        
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto  p-2 remaining--credits">
            <!-- Credits: 12 -->
             {{ `Credits: ${ $page?.props?.authenticatedUser?.credit_balance?.remaining_credit_points || 0 }`  }}
        </ul>
    </nav>
    </div>
</template>

<script setup>

</script>