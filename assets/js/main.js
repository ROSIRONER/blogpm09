document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => nav.classList.toggle('open'));
  }

  initLikeButtons(document);

  const commentForm = document.querySelector('#comment-form');
  const commentsSection = document.querySelector('.comments');

  if (commentForm && commentsSection) {
    commentForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const formData = new FormData(commentForm);
      formData.append('post_id', commentsSection.getAttribute('data-post-id') || '0');

      try {
        const response = await fetch(`${window.APP_BASE_URL}/add_comment.php`, {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();
        if (!response.ok) {
          alert(data.error || 'Не удалось добавить комментарий');
          return;
        }

        const wrapper = document.createElement('article');
        wrapper.className = 'comment';
        wrapper.innerHTML = `
          <div class="meta">
            <span>${escapeHtml(data.author)}</span>
            <span>${escapeHtml(data.created_at)}</span>
          </div>
          <p>${escapeHtml(data.content)}</p>
          <button class="like-button" data-like-key="comment-${Number(data.id)}" data-label="комментария">👍 <span>0</span></button>
        `;

        const list = document.querySelector('#comment-list');
        if (list) {
          list.prepend(wrapper);
          initLikeButtons(wrapper);
        }

        commentForm.reset();
      } catch {
        alert('Ошибка сети. Попробуйте позже.');
      }
    });
  }
});

function initLikeButtons(root) {
  root.querySelectorAll('.like-button').forEach((button) => {
    if (button.dataset.bound === '1') {
      return;
    }

    const key = button.dataset.likeKey;
    const counter = button.querySelector('span');

    if (key && counter) {
      const saved = Number(localStorage.getItem(`like:${key}`) || 0);
      counter.textContent = String(saved);
    }

    button.addEventListener('click', () => {
      if (!counter) return;
      const current = Number(counter.textContent || 0);
      const next = current + 1;
      counter.textContent = String(next);

      if (key) {
        localStorage.setItem(`like:${key}`, String(next));
      }

      button.classList.remove('is-burst');
      // force reflow for re-trigger animation
      // eslint-disable-next-line no-unused-expressions
      button.offsetWidth;
      button.classList.add('is-burst');
    });

    button.dataset.bound = '1';
  });
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}
