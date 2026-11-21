// Map task emoji (stored in the DB) to Bootstrap Icons + a colour tone.
const ICONS = {
  "☕": { icon: "bi-cup-hot-fill", hue: "amber" },
  "🚶": { icon: "bi-person-walking", hue: "green" },
  "📧": { icon: "bi-envelope-check-fill", hue: "blue" },
  "👏": { icon: "bi-hand-thumbs-up-fill", hue: "pink" },
  "💧": { icon: "bi-droplet-fill", hue: "blue" },
  "🧘": { icon: "bi-flower1", hue: "violet" },
  "🥗": { icon: "bi-egg-fried", hue: "green" },
  "🤝": { icon: "bi-people-fill", hue: "indigo" },
  "✨": { icon: "bi-stars", hue: "amber" },
  "🎧": { icon: "bi-headphones", hue: "violet" },
  "👋": { icon: "bi-emoji-smile-fill", hue: "pink" },
  "🏁": { icon: "bi-flag-fill", hue: "green" },
};

document.addEventListener("DOMContentLoaded", function () {
  fetch("bingo_data.php")
    .then((response) => response.json())
    .then((data) => {
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

      data.tasks.forEach((task) => {
        if (document.querySelector(`[data-task-id="${task.game_task_id}"]`)) {
          return;
        }

        const card = document.createElement("div");
        card.classList.add("bingo-card");
        card.dataset.taskId = task.game_task_id;
        if (task.completed) card.classList.add("is-completed");

        const iconWrap = document.createElement("div");
        iconWrap.classList.add("icon-wrap");

        const meta = ICONS[task.icon] || { icon: "bi-check2-circle", hue: "indigo" };
        iconWrap.classList.add(`tone-${meta.hue}`);
        if (/\.(png|svg|jpg|jpeg|webp)$/i.test(task.icon || "")) {
          const img = document.createElement("img");
          img.src = `../../icons/${task.icon}`;
          img.alt = task.title;
          img.classList.add("bingo-icon");
          iconWrap.appendChild(img);
        } else {
          const i = document.createElement("i");
          i.className = `bi ${meta.icon} bingo-icon`;
          i.setAttribute("aria-hidden", "true");
          iconWrap.appendChild(i);
        }

        const badge = document.createElement("span");
        badge.className = "done-badge";
        badge.innerHTML = '<i class="bi bi-check-lg"></i>';
        card.appendChild(badge);
        card.appendChild(iconWrap);

        const title = document.createElement("h5");
        title.textContent = task.title;
        card.appendChild(title);

        const points = document.createElement("p");
        points.classList.add("points");
        points.textContent = `${task.points} pts`;
        card.appendChild(points);

        if (task.description) {
          const description = document.createElement("p");
          description.classList.add("task-description");
          description.textContent = task.description;
          card.appendChild(description);
        }

        const button = document.createElement("button");
        setButton(button, task.completed);
        button.classList.add("complete-task");

        if (task.completed) {
          button.classList.add("completed");
        }

        button.addEventListener("click", function () {
          let action = task.completed ? "undo" : "complete";

          fetch("bingo_mark_task.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              task_id: task.game_task_id,
              action: action,
            }),
          })
            .then((response) => response.json())
            .then((result) => {
              if (result.success) {
                task.completed = task.completed ? 0 : 1;
                card.classList.toggle("is-completed", !!task.completed);
                setButton(button, task.completed);
                button.classList.toggle("completed");

                // Update score dynamically
                let currentScore = parseInt(
                  document.getElementById("user-score").textContent,
                );
                document.getElementById("user-score").textContent =
                  task.completed
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

function setButton(button, completed) {
  button.innerHTML = completed
    ? '<i class="bi bi-check-circle-fill"></i> Completed'
    : '<i class="bi bi-circle"></i> Mark as done';
}
