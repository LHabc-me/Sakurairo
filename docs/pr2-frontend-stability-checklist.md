# PR2 Frontend Stability Regression Checklist

## Run Script

```bash
./tools/check-pr2-frontend-stability.sh
```

## Coverage

1. JS syntax baseline (`js/nav.js`, `js/page.js`)
2. `nav.js` lifecycle contract markers (`init/destroy/rebind`)
3. Manual smoke flow:
   - page switch (home <-> article)
   - dark/light theme toggle
   - mobile menu and toc panel interactions
   - no new console errors after above actions

## Expected Result

- Script exits with code `0`
- Manual checklist passes without UI jitter or duplicated menu/nav reactions
