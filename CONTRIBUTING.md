# How to Contribute: A Fun Guide to Our Development Process

Hey there, future contributor! 👋 Welcome to our project. We're thrilled you're interested in helping out. This guide will walk you through our development process, from setting up your environment to getting your changes merged. Let's dive in.

## 1. Our Branch Structure

First things first, let's talk about our branches. We keep things simple:

- `1.x`, `2.x`, etc.: These are our release branches. The latest one (let's say `2.x`) is our default branch.
- `master`: This is our pristine production branch. Fancy, huh?

For us developers, we also use:

- A working branch (we suggest calling it `main`, but you do you!)
- A development branch for the next major version (maybe call it `develop`? Your choice!)

Remember, `main` and `develop` are just for us developers - they don't live on GitHub. It's like our secret clubhouse! 🏠

## 2. How to Contribute (For External Contributors)

### Step 1: Fork and Clone

1. Head over to our GitHub repo and hit that Fork button. It's like making your own copy of our project playground!
2. Clone your fork locally:
   ```bash
   git clone https://github.com/your-username/repo-name.git
   cd repo-name
   ```
3. Add our original repo as a remote (we call it `upstream`):
   ```bash
   git remote add upstream https://github.com/original-owner/repo-name.git
   ```

### Step 2: Create a Branch and Make Changes

1. Create a new branch for your amazing feature or fix:
   ```bash
   git checkout -b your-feature-branch
   ```
2. Make your changes. Go wild! (But also, maybe check our coding standards? 😉)

### Step 3: Commit Your Changes

Here's where it gets a bit formal. We use conventional commit messages. Don't worry, it's not as scary as it sounds!

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

For `<type>`, use one of these:

- `fix`: for bug fixes (patch release)
- `refactor`: for code changes that neither fix a bug nor add a feature (patch release)
- `docs`: for documentation changes (patch release)
- `issue`: for addressing an issue that's not a bug (patch release)
- `wip`: for work in progress (no release)
- `chore`: for other changes that don't modify src or test files (no release)
- `style`: for changes that do not affect the meaning of the code (no release)
- `test`: for adding missing tests or correcting existing tests (no release)

**Important:** Don't use `breaking` or `feat` types unless you're a core developer. We'll reject PRs with these types faster than you can say "semantic versioning"!

For more details, check out [Conventional Commits](https://www.conventionalcommits.org/). It's a thrilling read, we promise! 😴

### Step 4: Stay Up to Date

Before you push, make sure you're up to date with our latest changes:

```bash
git fetch upstream
git checkout your-feature-branch
git rebase upstream/2.x  # Or the current release branch
```

### Step 5: Push and Create a Pull Request

1. Push your changes to your fork:
   ```bash
   git push origin your-feature-branch
   ```
2. Go to GitHub and create a pull request to our current `2.x` branch.
3. Wait for our review. We promise we don't bite!

## 3. Developer Workflow (For Core Team Members)

Hey, core developer! Here's your special workflow:

1. Work on your local `main` branch for the current `2.x` updates.
2. When you're ready to contribute:
   - Push your `main` branch to your fork
   - Create a pull request from your fork's `main` to our repo's `2.x`
3. After approval and merging, update your local setup:
   ```bash
   git checkout 2.x
   git pull upstream 2.x
   git checkout main
   git rebase 2.x
   ```

## 4. Semantic Release: Our Magic Release Wand 🪄

We use semantic-release to automate our version management and package publishing. It's like having a robot assistant for releases!

Here's how it works with our commit types:

```javascript
releaseRules: [
  { type: 'breaking', release: 'major' },
  { type: 'feat', release: 'minor' },
  { type: 'fix', release: 'patch' },
  { type: 'refactor', release: 'patch' },
  { type: 'docs', release: 'patch' },
  { type: 'issue', release: 'patch' },
  { type: 'wip', release: false },
  { type: 'chore', release: false },
  { scope: 'style', release: false },
  { scope: 'test', release: false },
];
```

Remember, only core developers should use `breaking` or `feat` types. For everyone else, stick to patch-level changes or below. We're all about that stable life! 😎

## 5. Wrapping Up

And there you have it! You're now equipped with all the knowledge you need to contribute to our project. Remember, we're all here to learn and grow together. If you have any questions, don't hesitate to reach out.

Happy coding, and may the fork be with you! 🍴✨
