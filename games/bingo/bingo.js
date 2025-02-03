document.addEventListener("DOMContentLoaded", function () {
    fetch("bingo_data.php")
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("No active game period.");
                return;
            }

            // Set game period
            document.getElementById("game-start").textContent = data.start_date;
            document.getElementById("game-end").textContent = data.end_date;

            // Set user score
            document.getElementById("user-score").textContent = data.user_score;

            const board = document.getElementById("bingo-board");
            board.innerHTML = ""; // Clears the board before rendering new tasks

            data.tasks.forEach(task => {
                if (document.querySelector(`[data-task-id="${task.game_task_id}"]`)) {
                    return;
                }

                const card = document.createElement("div");
                card.classList.add("bingo-card");
                card.dataset.taskId = task.game_task_id;

                if (task.icon) {
                    const img = document.createElement("img");
                    img.src = `../../icons/${task.icon}`;
                    img.alt = task.title;
                    img.classList.add("bingo-icon");
                    card.appendChild(img);
                }

                const title = document.createElement("h5");
                title.textContent = task.title;
                card.appendChild(title);

                const points = document.createElement("p");
                points.textContent = `${task.points} Points`;
                card.appendChild(points);

                const button = document.createElement("button");
                button.textContent = task.completed ? "Completed" : "Mark as Done";
                button.classList.add("complete-task");

                if (task.completed) {
                    button.classList.add("completed");
                }

                button.addEventListener("click", function () {
                    let action = task.completed ? "undo" : "complete";

                    fetch("bingo_mark_task.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ task_id: task.game_task_id, action: action })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            task.completed = task.completed ? 0 : 1;
                            button.textContent = task.completed ? "Completed" : "Mark as Done";
                            button.classList.toggle("completed");

                            // Update score dynamically
                            let currentScore = parseInt(document.getElementById("user-score").textContent);
                            document.getElementById("user-score").textContent = task.completed 
                                ? currentScore + task.points 
                                : currentScore - task.points;
                        }
                    });
                });

                card.appendChild(button);
                board.appendChild(card);
            });
        });
});
