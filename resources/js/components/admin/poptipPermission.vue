<template>
  <Tooltip placement="right" max-width="200">
    <Button type="dashed" size="small">查看</Button>
    <div slot="content" style="white-space: normal;">
      <Tag color="primary" v-for="(item,index) in permissions" :key="index">{{item}}</Tag>
    </div>
  </Tooltip>
</template>

<script>
    export default {
        props: {
            permissions: Array
        },
    }
</script>