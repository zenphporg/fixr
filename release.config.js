const commitAnalyzerOptions = {
  preset: 'conventionalcommits',
  releaseRules: [
    { type: 'breaking', release: 'major' },
    { type: 'feat', release: 'minor' },
    { type: 'fix', release: 'patch' },
    { type: 'refactor', release: 'patch' },
    { type: 'docs', release: 'patch' },
    { type: 'task', release: 'patch' },
    { type: 'issue', release: 'patch' },
    { type: 'wip', release: false },
    { type: 'chore', release: false },
    { scope: 'style', release: false },
    { scope: 'test', release: false },
  ],
  parserOpts: {
    noteKeywords: [],
  },
};

const releaseNotesGeneratorOptions = {
  writerOpts: {
    transform: (commit, context) => {
      const issues = [];

      const types = {
        breaking: 'Breaking',
        feat: 'Features',
        fix: 'Bug Fixes',
        refactor: 'Code Refactoring',
        docs: 'Documentation',
        task: 'Code or other task',
        issue: 'Non-bug Issue Resolved',
        wip: 'Work in Progress',
        chore: 'Maintenance',
        style: 'Code Style Adjustments',
        test: 'Code Testing',
      };

      commit.type = types[commit.type];

      if (typeof commit.hash === 'string') {
        commit.shortHash = commit.hash.substring(0, 7);
      }

      if (typeof commit.subject === 'string') {
        let url = context.repository ? `${context.host}/${context.owner}/${context.repository}` : context.repoUrl;
        if (url) {
          url = `${url}/issues/`;
          // Issue URLs.
          commit.subject = commit.subject.replace(/#([0-9]+)/g, (_, issue) => {
            issues.push(issue);
            return `[#${issue}](${url}${issue})`;
          });
        }
        if (context.host) {
          // User URLs.
          commit.subject = commit.subject.replace(/\B@([a-z0-9](?:-?[a-z0-9/]){0,38})/g, (_, username) => {
            if (username.includes('/')) {
              return `@${username}`;
            }

            return `[@${username}](${context.host}/${username})`;
          });
        }
      }

      // remove references that already appear in the subject
      commit.references = commit.references.filter((reference) => {
        if (issues.indexOf(reference.issue) === -1) {
          return true;
        }

        return false;
      });

      return commit;
    },
  },
};

export default {
  debug: true,
  branches: [
    '+([0-9])?(.{+([0-9]),x}).x',
    'main',
    { name: 'beta', prerelease: true },
    { name: 'alpha', prerelease: true },
  ],
  repositoryUrl: 'https://github.com/zenphporg/fixr',

  plugins: [
    ['@semantic-release/commit-analyzer', commitAnalyzerOptions],
    ['@semantic-release/release-notes-generator', releaseNotesGeneratorOptions],
    [
      '@semantic-release/changelog',
      {
        changelogFile: 'CHANGELOG.md',
        changelogTitle: '# Release Notes',
      },
    ],
    [
      'semantic-release-replace-plugin',
      {
        replacements: [
          {
            files: ['config/app.php'],
            from: "'version' => '.*'",
            to: "'version' => '${nextRelease.version}'",
            results: [
              {
                file: 'config/app.php',
                hasChanged: true,
                numMatches: 1,
                numReplacements: 1,
              },
            ],
            countMatches: true,
          },
        ],
      },
    ],
    [
      '@semantic-release/exec',
      {
        prepareCmd: 'php fixr app:build',
      },
    ],
    [
      '@semantic-release/git',
      {
        assets: ['CHANGELOG.md', 'config/app.php', 'builds/fixr'],
        message: 'chore(release): ${nextRelease.version} [skip ci]\n\n${nextRelease.notes}',
      },
    ],
    [
      '@semantic-release/github',
      {
        publish: true,
      },
    ],
  ],
};
