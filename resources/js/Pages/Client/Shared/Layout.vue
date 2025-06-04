<template>
    
    <SideMenu />
    <HeaderSubMenu />
    <slot></slot>
</template>
<script setup>
import SideMenu from './SideMenu.vue'
import HeaderSubMenu from './HeaderSubMenu.vue'
</script>