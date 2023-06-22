import { test, expect } from '@playwright/test';
import { pluginCookieBaseName, querySelector, samplePost } from './constants';

const messageFactory = () => {
    return `And here is my opinion on ${new Date()}`;
};

const cookieName = () => {
    return `${pluginCookieBaseName}${samplePost.id}`;
};

test('the message input remains empty when there is no previously saved message', async ({
    page,
    context,
}) => {
    await page.goto(samplePost.path);

    // WP doesn't set any cookies, so this should be empty
    await expect(await context.cookies()).toHaveLength(0);
    await expect(page.locator(querySelector.messageField)).toHaveValue('');
});

test('the previously saved message is applied when the post is loaded', async ({
    page,
    context,
}) => {
    const message = messageFactory();

    // The cookie has to be set manually
    await context.addCookies([
        {
            name: cookieName(),
            value: encodeURIComponent(message),
            path: '/',
            // new URL(...).host and process.env if environmental variables were used
            // The host from the baseURL set in the config
            domain: 'wordpress',
        },
    ]);

    await page.goto(samplePost.path);

    await expect(await context.cookies()).toMatchObject([
        {
            name: cookieName(),
            value: encodeURIComponent(message),
        },
    ]);
    await expect(page.locator(querySelector.messageField)).toHaveValue(message);
});

test('the message is saved when the comment is not submitted successfully', async ({
    page,
    context,
}) => {
    const message = messageFactory();

    await page.goto(samplePost.path);

    // The other required fields are left empty on purpose
    await page.locator(querySelector.messageField).fill(message);
    await page.locator(querySelector.submitButton).click();

    // The error message https://github.com/WordPress/WordPress/blob/84e9601e5a2966c0aa80020bbf0c043dd8b6bfbb/wp-includes/comment.php#L3631
    await expect(page.locator(querySelector.errorDieMessage)).toContainText(
        /Please fill the required fields/
    );
    await expect(await context.cookies()).toMatchObject([
        {
            name: cookieName(),
            value: encodeURIComponent(message),
        },
    ]);
});

test('there is no saved message when the comment is submitted successfully', async ({
    page,
    context,
}) => {
    const message = messageFactory();

    await page.goto(samplePost.path);

    await page.locator(querySelector.messageField).fill(message);
    await page.locator(querySelector.authorField).fill('Somebody');
    await page.locator(querySelector.emailField).fill('somebody@testing.here');
    await page.locator(querySelector.submitButton).click();

    // We should back where we started
    await expect(page.url()).toContain(samplePost.path);
    await expect(
        page.locator(querySelector.commentContent).last()
    ).toContainText(message);
    await expect(await context.cookies()).toHaveLength(0);
});
