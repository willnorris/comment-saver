document.addEventListener('DOMContentLoaded', function () {
    const cookieName =
        window.comment_saver_cookie?.name ??
        `comment_saver_post${window.location.pathname}`;

    function getSavedCommentMessage() {
        const cookies = document.cookie.split('; ');
        const matchedCurrentPostCookies = cookies.filter((cookie) =>
            cookie.startsWith(`${cookieName}=`)
        );

        if (matchedCurrentPostCookies.length === 0) {
            return '';
        }

        const [, value] = matchedCurrentPostCookies[0].split('=');

        return decodeURIComponent(value);
    }

    function saveCommentMessage(message) {
        const encodedMessage = encodeURIComponent(message);
        const path = window.comment_saver_cookie?.path ?? '/';
        const lifeTime = 60 * 60; // one hour

        document.cookie = `${cookieName}=${encodedMessage}; path=${path}; max-age=${lifeTime}`;
    }

    const commentFormEl = document.querySelector('#commentform');
    const commentMessageFieldEl = document.querySelector('#comment');

    if (commentFormEl === null || commentMessageFieldEl === null) {
        return;
    }

    commentFormEl.addEventListener('submit', function () {
        saveCommentMessage(commentMessageFieldEl.value);
    });

    const savedMessage = getSavedCommentMessage();

    if (savedMessage !== '') {
        commentMessageFieldEl.value = savedMessage;
    }
});
