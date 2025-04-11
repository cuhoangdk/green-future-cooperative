<template>
    <div>
        <LayoutHeader />
        <div>
            <slot />
        </div>
        <LayoutSocialButtons style="z-index: 10;"/>
        <LayoutFooter />
        <LayoutDock />
    </div>
</template>