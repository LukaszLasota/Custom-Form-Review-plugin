import defaultConfig from "@wordpress/scripts/config/webpack.config.js";
import path from 'path';

export default {
  ...defaultConfig,
  entry: {
    ...defaultConfig.entry(),
  },
  output: {
    path: path.resolve(process.cwd(), "build"),
    filename: "[name].js",
  },
};


