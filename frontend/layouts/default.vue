<template>
    <div>
        <LayoutHeader />
        <div class="mt-[58px] lg:mt-0">
            <slot />
        </div>
        <LayoutSocialButtons style="z-index: 10;"/>
        <LayoutFooter />
        <LayoutDock />
    </div>
</template>