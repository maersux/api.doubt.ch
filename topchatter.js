const got = require("got");
const config = require("../../config.json");
const checkChannels = [
  "revedtv",
  "annitheduck",
  "fenl4",
  "lucas19961",
  "ninskatv",
  "papaplatte",
  "auchtoni",
  "mahluna",
];

module.exports = {
  name: "topchatter",
  description: "Zeigt die Topchatter von einem Channel",
  aliases: ["tct", "tc"],
  async execute(client, msg, utils) {
    let i;

    function response(text, addAppendix = true) {
      if (addAppendix) text += ' Chatting';
      return { text: text, reply: true }
    }

    function invalidArguments() {
      const text = `Options: ${msg.prefix}${msg.commandName} <se | alltime | today | yesterday | month> <Channel> oder ${msg.prefix}${msg.commandName} date <Channel> <Date (Format: 2022-06-26)>)`;
      return response(text, false);
    }

    if (msg.args.length < 2) return invalidArguments();

    const option = msg.args[0].toLowerCase();
    const channel = msg.args[1]?.replace("@", "").toLowerCase();
    const date = msg.args[2];
    const logging = await utils.query(`SELECT login FROM channels WHERE login='${channel}'`);
    const logging2 = await utils.query(`SELECT logging AS zahl FROM channels WHERE login='${channel}'`);

    if (!logging.length) {
      return response("Dieser Channel ist nicht in meiner Datenbank.", false);
    }

    if (logging2[0].zahl === 0) {
      return response(`Der Channel ${utils.antiping(channel)} ist vom Logging ausgenommen.`)
    }

    try {
      switch (option) {
        case "se": {
          msg.send("Waiting OBOY");

          const topChatterSE = await got(`https://api.streamelements.com/kappa/v2/chatstats/${channel}/stats`).json();
          const botsToIgnore = [
            "streamelements", "fossabot",
            "apulxd", "nightbot",
            "streamlabs", "moobot",
            "wizebot", "enationbot",
            "mirrobot", "restreambot",
            "own3d", "ohbot",
            "wzbot", "botisimo",
            "soundalerts", "mitsukibot",
            "t󠀀hepositivebot", "supibot",
          ];

          const topChatter = topChatterSE.chatters
              .filter((chatter, i) => i < 10 && !botsToIgnore.includes(channel.name))
              .map((chatter, i) => `${i + 1}. ${utils.antiping(chatter.name)} => ${utils.formatNumber(chatter.amount)}`)

          let text = `Die aktivsten Chatter alltime im Channel ${utils.antiping(channel)} sind: `
          text += topChatter.join(" ").replace("y󠀀ung_jibbit", "TrollDespair");

          return response(text)
        }

        case "alltime": {
          msg.send("Waiting OBOY");

          const topChatter = await utils.query(
            `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
            msg.channel.login
          );

          if (!topChatter.length) {
            return response(`Im Channel ${utils.antiping(channel)} wurde noch nie gechattet.`);
          }

          let text = `Die aktivsten Chatter alltime im Channel ${utils.antiping(channel)} sind: `;

          topChatter.slice(0, 10).forEach((to, index) => {
            text += ` ${index + 1}. ${utils.antiping(to.user_login)} => ${utils.formatNumber(to.message_count)} `;
          });

          return response(text);
        }

        case "all": {
          msg.send("Waiting OBOY");

          const topChatter = await utils.query(
            `SELECT user_login, COUNT(id) AS message_count FROM ${channel} GROUP BY user_login ORDER BY message_count DESC LIMIT 100`,
            msg.channel.login,
          );

          if (!topChatter.length) {
            return response(`Im Channel ${utils.antiping(channel)} wurde noch nie gechattet.`)
          }

          let text = `Die Top 100 aktivsten Chatter alltime im Channel ${utils.antiping(channel)} sind: `;

          topChatter.slice(0, 100).forEach(function(to, i) {
            text += ` ${i + 1}. ${utils.antiping(to.user_login)} => ${utils.formatNumber(to.message_count)} `;
          });

          return response(text);
        }

        case "today": {
          const topChatterToday = await utils.query(
            `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE timestamp > CURDATE() AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
            msg.channel.login,
          );

          if (!topChatterToday) {
            return response(`Im Channel ${utils.antiping(channel)} wurde noch nie gechattet.`);
          }

          let text = `Die aktivsten Chatter im Channel ${utils.antiping(channel)} heute sind: `;

          topChatterToday.slice(0, 10).forEach(function(tct, i) {
            text += ` ${i + 1}. ${utils.antiping(tct.user_login)} => ${utils.formatNumber(tct.message_count)} `;
          });

          return response(text);
        }

        case "yesterday": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} today <Channel>`,
              reply: true,
            };

          try {
            const topchatteryesterday = await utils.query(
              `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE timestamp >= CURDATE() - INTERVAL 1 DAY AND timestamp < CURDATE() AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
              msg.channel.login,
            );

            text = `Die aktivsten Chatter im Channel: ${utils.antiping(
              channel,
            )} waren gestern`;
            i = 0;
            topchatteryesterday.forEach(function (tcy) {
              i += 1;
              if (i < 11) {
                text += ` ${i}. ${utils.antiping(
                  tcy.user_login,
                )} => ${utils.formatNumber(tcy.message_count)} `;
              }
            });
            if (!topchatteryesterday.length)
              return {
                text: `Im Channel ${utils.antiping(
                  channel,
                )} wurde gestern nicht gechattet.`,
                reply: true,
              };

            return { text: text + `Chatting`, reply: true };
          } catch (e) {
            return { text: `FeelsDankMan Error`, reply: true };
          }
        }
        case "month": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} month <Channel>`,
              reply: true,
            };
          const topchattermonth = await utils.query(
            `
                  SELECT m.user_login, COUNT(m.id) AS message_count
                  FROM ${channel} m
                  WHERE
                    MONTH(m.timestamp) = MONTH(CURRENT_TIMESTAMP)
                    AND YEAR(m.timestamp) = YEAR(CURRENT_TIMESTAMP)
                    AND m.channel_login = ?
                  GROUP BY m.user_login
                  ORDER BY message_count DESC
                  LIMIT 10
                `,
            [channel],
          );

          let text = `Die aktivsten Chatter im Channel ${channel} waren diesen Monat:`;

          topchattermonth.forEach(function (tcm, index) {
            const rank = index + 1;
            text += ` ${rank}. ${utils.antiping(
              tcm.user_login,
            )} => ${utils.formatNumber(tcm.message_count)} `;
          });

          if (topchattermonth.length === 0) {
            text = `Im Channel ${channel} wurde diesen Monat nicht gechattet.`;
          }
          return { text: text + `Chatting`, reply: true };
        }
        case "year": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} month <Channel>`,
              reply: true,
            };

          try {
            const topchattermonth = await utils.query(
              `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE DATE_FORMAT(timestamp, '%Y') = YEAR(CURDATE()) AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
              msg.channel.login,
            );

            text = `Die aktivsten Chatter im Channel: ${utils.antiping(
              channel,
            )} sind dieses Jahr`;
            i = 0;
            topchattermonth.forEach(function (tcm) {
              i += 1;
              if (i < 11) {
                text += ` ${i}. ${utils.antiping(
                  tcm.user_login,
                )} => ${utils.formatNumber(tcm.message_count)} `;
              }
            });
            if (!topchattermonth.length)
              return {
                text: `Im Channel ${utils.antiping(
                  channel,
                )} wurde dieses Jahr noch nicht gechattet.`,
                reply: true,
              };

            return { text: text + `Chatting`, reply: true };
          } catch (e) {
            return { text: `FeelsDankMan Error`, reply: true };
          }
        }
        case "lastyear": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} month <Channel>`,
              reply: true,
            };

          try {
            const topchattermonth = await utils.query(
              `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE YEAR(timestamp)=YEAR(CURDATE() - INTERVAL 1 YEAR) AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
              msg.channel.login,
            );

            text = `Die aktivsten Chatter im Channel: ${utils.antiping(
              channel,
            )} waren letztes Jahr`;
            i = 0;
            topchattermonth.forEach(function (tcm) {
              i += 1;
              if (i < 11) {
                text += ` ${i}. ${utils.antiping(
                  tcm.user_login,
                )} => ${utils.formatNumber(tcm.message_count)} `;
              }
            });
            if (!topchattermonth.length)
              return {
                text: `Im Channel ${utils.antiping(
                  channel,
                )} wurde letztes Jahr nicht gechattet.`,
                reply: true,
              };

            return { text: text + `Chatting`, reply: true };
          } catch (e) {
            return { text: `FeelsDankMan Error`, reply: true };
          }
        }
        case "lastmonth": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} lastmonth <Channel>`,
              reply: true,
            };

          try {
            const topchattermonth = await utils.query(
              `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE MONTH(timestamp) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(timestamp) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))  AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 10`,
              msg.channel.login,
            );

            text = `Die aktivsten Chatter im Channel: ${utils.antiping(
              channel,
            )} waren letzten Monat`;
            i = 0;
            topchattermonth.forEach(function (tcm) {
              i += 1;
              if (i < 11) {
                text += ` ${i}. ${utils.antiping(
                  tcm.user_login,
                )} => ${utils.formatNumber(tcm.message_count)} `;
              }
            });
            if (!topchattermonth.length)
              return {
                text: `Im Channel ${utils.antiping(
                  channel,
                )} wurde letzten Monat nicht gechattet.`,
                reply: true,
              };

            return { text: text + `Chatting`, reply: true };
          } catch (e) {
            return { text: `FeelsDankMan Error`, reply: true };
          }
        }
        case "date": {
          if (msg.args.length < 2)
            return {
              text: `Usage: ${msg.prefix}${msg.commandName} date <Channel> <Date> (Jahr-Monat-Tag z.b(2022-06-26))`,
              reply: true,
            };

          try {
            const topchatterdate = await utils.query(
              `SELECT user_login, COUNT(id) AS message_count FROM ${channel} WHERE timestamp LIKE '%${date}%' AND channel_login='${channel}' GROUP BY user_login ORDER BY message_count DESC LIMIT 5`,
              msg.channel.login,
            );

            text = `Die aktivsten Chatter im Channel: ${utils.antiping(
              channel,
            )} waren am ${date}`;
            i = 0;
            topchatterdate.forEach(function (tcd) {
              i += 1;
              if (i < 6) {
                text += ` ${i}. ${utils.antiping(
                  tcd.user_login,
                )} => ${utils.formatNumber(tcd.message_count)} `;
              }
            });
            if (!topchatterdate.length)
              return {
                text: `Im Channel ${utils.antiping(
                  channel,
                )} wurde am ${date} nicht gechattet.`,
                reply: true,
              };
            return { text: text + `Chatting`, reply: true };
          } catch (e) {
            return { text: `FeelsDankMan Error`, reply: true };
          }
        }

        default: {
          return invalidArguments();
        }
      }

    } catch(e) {
      return response(`FeelsDankMan Error ${e}`)
    }
  },
};
