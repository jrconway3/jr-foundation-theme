document.documentElement.classList.add('jr-theme-foundation-ready');

document.addEventListener('DOMContentLoaded', function () {
  // Collapse an active video card and clear its iframe src.
  function collapseCard(card) {
    var toggle  = card.querySelector('.video-card__toggle');
    var embed   = card.querySelector('.video-card__embed');
    var iframe  = embed ? embed.querySelector('iframe') : null;
    var hadFocus = embed && embed.contains(document.activeElement);
    card.classList.remove('active');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
    if (embed)  embed.setAttribute('hidden', '');
    if (iframe) iframe.src = 'about:blank';
    // Return focus to the toggle so keyboard users aren't stranded.
    if (hadFocus && toggle) toggle.focus();
  }

  // Expand a video card and load the YouTube embed.
  function expandCard(card) {
    var videoId = card.dataset.videoId;
    if (!videoId) return;
    // Only accept well-formed YouTube IDs (11 alphanumeric/dash/underscore chars).
    if (!/^[A-Za-z0-9_-]{11}$/.test(videoId)) return;
    var toggle = card.querySelector('.video-card__toggle');
    var embed  = card.querySelector('.video-card__embed');
    var iframe = embed ? embed.querySelector('iframe') : null;
    if (!embed || !iframe) return;
    iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1';
    embed.removeAttribute('hidden');
    card.classList.add('active');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
    // Move focus to the close button so keyboard users can close or tab into the embed.
    var closeBtn = embed.querySelector('.video-embed-close');
    if (closeBtn) closeBtn.focus();
  }

  // Click handler — the toggle <button> fires click natively on Enter/Space,
  // so no separate keydown handler is needed.
  document.querySelectorAll('.video-strip').forEach(function (strip) {
    strip.addEventListener('click', function (e) {
      var card = e.target.closest('.video-card');
      if (!card) return;

      // Let anchor clicks and iframe events pass through without toggling.
      if (e.target.closest('a') || e.target.tagName === 'IFRAME') return;

      var wasActive = card.classList.contains('active');

      // Collapse all active cards across all strips.
      document.querySelectorAll('.video-card.active').forEach(collapseCard);

      if (!wasActive) {
        expandCard(card);
      }
    });
  });

  // Sidebar video list — expand/collapse inline embed on click.
  function collapseSidebarItem(item) {
    var btn      = item.querySelector('.sidebar-video-btn');
    var embed    = item.querySelector('.sidebar-video-embed');
    var iframe   = embed ? embed.querySelector('iframe') : null;
    var hadFocus = embed && embed.contains(document.activeElement);
    item.classList.remove('active');
    if (btn)    btn.setAttribute('aria-expanded', 'false');
    if (embed)  embed.setAttribute('hidden', '');
    if (iframe) iframe.src = 'about:blank';
    // Return focus to the button so keyboard users aren't stranded.
    if (hadFocus && btn) btn.focus();
  }

  function expandSidebarItem(item) {
    var btn     = item.querySelector('.sidebar-video-btn');
    var videoId = btn ? btn.dataset.videoId : null;
    if (!videoId || !/^[A-Za-z0-9_-]{11}$/.test(videoId)) return;
    var embed  = item.querySelector('.sidebar-video-embed');
    var iframe = embed ? embed.querySelector('iframe') : null;
    if (!embed || !iframe) return;
    iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1';
    embed.removeAttribute('hidden');
    item.classList.add('active');
    if (btn) btn.setAttribute('aria-expanded', 'true');
    // Move focus to the close button so keyboard users can close or tab into the embed.
    var closeBtn = embed.querySelector('.video-embed-close');
    if (closeBtn) closeBtn.focus();
  }

  document.querySelectorAll('.sidebar-video-list').forEach(function (list) {
    list.addEventListener('click', function (e) {
      var item = e.target.closest('.sidebar-video-item');
      if (!item) return;

      // Let anchor clicks and iframe events pass through without toggling.
      if (e.target.closest('a') || e.target.tagName === 'IFRAME') return;

      var wasActive = item.classList.contains('active');

      // Collapse all active sidebar items across all lists.
      document.querySelectorAll('.sidebar-video-item.active').forEach(collapseSidebarItem);

      if (!wasActive) {
        expandSidebarItem(item);
      }
    });
  });
});
