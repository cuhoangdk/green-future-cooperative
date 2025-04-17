<template>
    <div>
        <pre>{{ token }}</pre>
        <pre>{{ email }}</pre>
    </div>
</template>

<script setup lang="ts">

const { verifyAccount } = useCustomerAuth()

const token = useRoute().query.token as string
const email = useRoute().query.email as string

verifyAccount(email, token)
</script>