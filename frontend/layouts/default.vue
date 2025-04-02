<template>
    <div>
        <LayoutHeader />
        <div class="bg-gray-50">
            <slot />
        </div>
        <LayoutSocialButtons style="z-index: 10;"/>
        <LayoutFooter />
    </div>
</template>