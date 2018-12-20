<template>

    <!-- Jobcard menu dropdown -->
    <Dropdown trigger="click">
        <a href="javascript:void(0)">
            <Icon type="md-more" :size="16"></Icon>
        </a>
        <DropdownMenu slot="list">
            <DropdownItem>Edit</DropdownItem>
            <DropdownItem>Trash</DropdownItem>
            <DropdownItem>Add Client</DropdownItem>
            <DropdownItem>Add Supplier</DropdownItem>
            <DropdownItem>Add Labour</DropdownItem>
            <DropdownItem>Add Asset</DropdownItem>
        </DropdownMenu>
    </Dropdown>

</template>
<script>
    export default {};
</script>