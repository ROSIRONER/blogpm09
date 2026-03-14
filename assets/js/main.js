document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.main-nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', () => nav.classList.toggle('open'));
  }

  document.querySelectorAll('.like-button').forEach((button) => {
    button.addEventListener('click', () => {
      const counter = button.querySelector('span');
      const current = Number(counter?.textContent || 0);
      if (counter) counter.textContent = String(current + 1);
    });
  });

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
          <button class="like-button" data-label="комментария">👍 <span>0</span></button>
        `;

        const list = document.querySelector('#comment-list');
        if (list) {
          list.prepend(wrapper);
          const like = wrapper.querySelector('.like-button');
          like?.addEventListener('click', () => {
            const counter = like.querySelector('span');
            const current = Number(counter?.textContent || 0);
            if (counter) counter.textContent = String(current + 1);
          });
        }

        commentForm.reset();
      } catch {
        alert('Ошибка сети. Попробуйте позже.');
      }
    });
  }
});

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}
