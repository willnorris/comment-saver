export const pluginCookieBaseName = 'comment_saver_post';

export const samplePost = {
    path: '/hello-world/',
    id: 1,
} as const;

export const querySelector = {
    messageField: 'textarea#comment',
    authorField: 'input#author',
    emailField: 'input#email',
    submitButton: 'input#submit',
    commentContent: '.wp-block-comment-content',
    errorDieMessage: '.wp-die-message',
} as const;
