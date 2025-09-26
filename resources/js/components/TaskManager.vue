<template>
    <div class="container">
        <h4 class="center-align">Kanban Task Manager</h4>

        <!-- Top Row: Arrows + Date Picker + Add Task Button -->
        <div class="top-row">
            <div class="date-row">
                <button class="arrow-btn" @click="changeDate(-1)">
                    &#8592;
                </button>
                <input
                    type="date"
                    v-model="selectedDate"
                    @change="fetchTasksForDate"
                    class="date-input"
                />
                <button class="arrow-btn" @click="changeDate(1)">
                    &#8594;
                </button>
            </div>

            <a
                class="waves-effect waves-light btn add-task-btn"
                @click="openAddModal"
            >
                Add Task
            </a>
        </div>

        <!-- Kanban Columns -->
        <div class="row">
            <div
                class="col s12 m4"
                v-for="column in columns"
                :key="column.name"
            >
                <div class="card">
                    <div class="card-content">
                        <span class="card-title center-align">{{
                            column.name
                        }}</span>
                        <draggable
                            v-model="column.tasks"
                            group="tasks"
                            @end="onDragEnd"
                            item-key="id"
                            class="kanban-column"
                        >
                            <template #item="{ element }">
                                <div
                                    class="card z-depth-1 kanban-card"
                                    @click="openEditModal(element)"
                                >
                                    <div class="card-content">
                                        <span class="card-title">{{
                                            element.title
                                        }}</span>
                                        <p v-if="element.due_date">
                                            Due: {{ element.due_date }}
                                        </p>
                                    </div>
                                    <div class="card-action">
                                        <a
                                            href="#!"
                                            class="red-text"
                                            @click.stop.prevent="
                                                deleteTask(element.id)
                                            "
                                        >
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </draggable>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <div id="taskModal" class="modal">
            <div class="modal-content">
                <h5>{{ editingTask ? "Edit Task" : "Add Task" }}</h5>
                <div class="row">
                    <div class="input-field col s12">
                        <input type="text" v-model="taskTitle" id="title" />
                        <label for="title" class="active">Task Title</label>
                    </div>
                    <div class="input-field col s6">
                        <input
                            type="date"
                            v-model="taskDueDate"
                            id="due_date"
                        />
                        <label for="due_date" class="active">Due Date</label>
                    </div>
                    <div class="input-field col s6">
                        <select v-model="taskStatus">
                            <option value="pending">Pending</option>
                            <option value="progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a
                    href="#!"
                    class="modal-close waves-effect waves-green btn"
                    @click="editingTask ? updateTask() : addTask()"
                >
                    {{ editingTask ? "Update Task" : "Add Task" }}
                </a>
                <a
                    href="#!"
                    class="modal-close waves-effect waves-red btn grey"
                    @click="cancelEdit"
                >
                    Cancel
                </a>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <h5>Are you sure?</h5>
                <p>Do you really want to delete this task?</p>
            </div>
            <div class="modal-footer">
                <a
                    href="#!"
                    class="modal-close waves-effect waves-red btn grey"
                    @click="cancelDelete"
                >
                    Cancel
                </a>
                <a
                    href="#!"
                    class="modal-close waves-effect waves-green btn red"
                    @click="confirmDelete"
                >
                    Delete
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import draggable from "vuedraggable";

export default {
    components: { draggable },
    data() {
        return {
            tasks: [],
            columns: [
                { name: "Pending", tasks: [] },
                { name: "Progress", tasks: [] },
                { name: "Completed", tasks: [] },
            ],
            selectedDate: new Date().toISOString().substr(0, 10), // default today
            taskTitle: "",
            taskDueDate: "",
            taskStatus: "pending",
            editingTask: null,
            modalInstance: null,
            taskToDelete: null,
            deleteModalInstance: null,
        };
    },
    mounted() {
        this.fetchTasksForDate();

        // Initialize Materialize select and modal
        M.FormSelect.init(document.querySelectorAll("select"));
        const modals = document.querySelectorAll(".modal");
        const modalInstances = M.Modal.init(modals, { dismissible: false });
        this.modalInstance = modalInstances[0]; // task modal
        this.deleteModalInstance = modalInstances[1]; // delete modal
    },
    methods: {
        // Fetch tasks for selected date
        fetchTasksForDate() {
            axios
                .get(`/api/tasks?date=${this.selectedDate}`)
                .then((res) => {
                    this.tasks = res.data;
                    this.mapTasksToColumns();
                })
                .catch(console.error);
        },

        // Change date by days (-1 previous, +1 next)
        changeDate(days) {
            const date = new Date(this.selectedDate);
            date.setDate(date.getDate() + days);
            this.selectedDate = date.toISOString().substr(0, 10);
            this.fetchTasksForDate();
        },

        // Map tasks to Kanban columns
        mapTasksToColumns() {
            this.columns.forEach((col) => (col.tasks = []));
            this.tasks.forEach((task) => {
                if (task.status === "pending") this.columns[0].tasks.push(task);
                else if (task.status === "progress")
                    this.columns[1].tasks.push(task);
                else this.columns[2].tasks.push(task);
            });
        },

        // Drag & Drop update
        onDragEnd() {
            this.columns.forEach((col, idx) => {
                col.tasks.forEach((task) => {
                    task.status =
                        idx === 0
                            ? "pending"
                            : idx === 1
                            ? "progress"
                            : "completed";
                    axios
                        .put(`/api/tasks/${task.id}`, {
                            title: task.title,
                            due_date: task.due_date,
                            status: task.status,
                            task_date: this.selectedDate,
                        })
                        .catch(console.error);
                });
            });
        },

        openAddModal() {
            this.editingTask = null;
            this.taskTitle = "";
            this.taskDueDate = "";
            this.taskStatus = "pending";
            this.modalInstance.open();
            M.FormSelect.init(document.querySelectorAll("select"));
        },

        addTask() {
            if (!this.taskTitle)
                return M.toast({
                    html: "Task title required!",
                    classes: "red",
                });

            axios
                .post("/api/tasks", {
                    title: this.taskTitle,
                    due_date: this.taskDueDate,
                    status: this.taskStatus,
                    task_date: this.selectedDate,
                })
                .then((res) => {
                    this.tasks.push(res.data);
                    this.mapTasksToColumns();
                    this.modalInstance.close();
                });
        },

        openEditModal(task) {
            this.editingTask = task;
            this.taskTitle = task.title;
            this.taskDueDate = task.due_date;
            this.taskStatus = task.status;
            this.modalInstance.open();
            M.FormSelect.init(document.querySelectorAll("select"));
        },

        updateTask() {
            axios
                .put(`/api/tasks/${this.editingTask.id}`, {
                    title: this.taskTitle,
                    due_date: this.taskDueDate,
                    status: this.taskStatus,
                    task_date: this.selectedDate,
                })
                .then((res) => {
                    const index = this.tasks.findIndex(
                        (t) => t.id === this.editingTask.id
                    );
                    this.tasks.splice(index, 1, res.data);
                    this.mapTasksToColumns();
                    this.modalInstance.close();
                });
        },

        cancelEdit() {
            this.editingTask = null;
            this.modalInstance.close();
        },

        deleteTask(id) {
            this.taskToDelete = id;
            this.deleteModalInstance.open();
        },

        confirmDelete() {
            axios.delete(`/api/tasks/${this.taskToDelete}`).then(() => {
                this.tasks = this.tasks.filter(
                    (t) => t.id !== this.taskToDelete
                );
                this.mapTasksToColumns();
                this.taskToDelete = null;
            });
        },

        cancelDelete() {
            this.taskToDelete = null;
        },
    },
};
</script>

<style scoped>
.top-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.date-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    flex-wrap: nowrap;
}

.date-input {
    padding: 10px 16px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ccc;
    min-width: 160px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.date-input:focus {
    outline: none;
    border-color: #42a5f5;
    box-shadow: 0 2px 8px rgba(66, 165, 245, 0.3);
}

.arrow-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: 1px solid #ccc;
    font-size: 22px;
    background-color: #fff;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s, transform 0.2s;
}

.arrow-btn:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
}

.add-task-btn {
    white-space: nowrap;
    font-size: 16px;
    padding: 0 20px;
}

.kanban-column {
    min-height: 300px;
    padding: 10px;
}

.kanban-card {
    margin-bottom: 10px;
    cursor: grab;
}

.mb-3 {
    margin-bottom: 20px;
}

@media (max-width: 600px) {
    .arrow-btn {
        width: 38px;
        height: 38px;
        font-size: 20px;
    }

    .date-input {
        min-width: 130px;
        font-size: 14px;
        padding: 8px 12px;
    }

    .add-task-btn {
        font-size: 14px;
        padding: 0 14px;
    }

    .top-row {
        gap: 10px;
    }
}
</style>
