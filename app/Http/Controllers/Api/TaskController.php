<template>
  <div class="container mt-5">
    <h2>Task Manager</h2>
    <ul class="list-group mb-3">
      <li class="list-group-item" v-for="task in tasks" :key="task.id">
        <strong>{{ task.title }}</strong> - {{ task.description }} - <em>{{ task.status }}</em>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      tasks: [
        { id: 1, title: 'Task A', description: 'Task A Description', status: 'completed' },
        { id: 2, title: 'Task B', description: 'Task B Description', status: 'pending' }
      ]
    };
  }
}
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: auto;
}
h2 {
  margin-bottom: 20px;
}
</style>
