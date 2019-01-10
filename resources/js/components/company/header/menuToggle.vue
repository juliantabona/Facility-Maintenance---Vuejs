<template>

    <!-- Company menu dropdown -->
    <Dropdown trigger="click">
        <a href="javascript:void(0)">
            <Icon type="md-more" :size="16"></Icon>
        </a>
        <DropdownMenu slot="list">
            <DropdownItem>Edit</DropdownItem>
            <DropdownItem>Remove</DropdownItem>
            <DropdownItem>Add Contact</DropdownItem>
            <DropdownItem>Download Profile</DropdownItem>
            <DropdownItem>Download Logo</DropdownItem>
        </DropdownMenu>
    </Dropdown>

</template>
<script>
    export default {};
</script>