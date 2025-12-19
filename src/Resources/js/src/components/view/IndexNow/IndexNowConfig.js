import React from "react";
import {observer} from "mobx-react";
import {action, observable} from "mobx";
import {Loader} from "sulu-admin-bundle/components";
import {withToolbar} from "sulu-admin-bundle/containers";
import {translate} from "sulu-admin-bundle/utils";
import {Requester} from "sulu-admin-bundle/services";
import './indexNowConfig.scss';

@observer
class IndexNowConfig extends React.Component {
    @observable loading = false;
    @observable data = {urls: [], responses: {}};

    componentDidMount() {
        this.loadUrls().then();
    }

    @action loadUrls = () => {
        this.loading = true;

        return Requester.get("/admin/api/index-now/urls")
            .then(action((response) => {
                    this.data.urls = response.urls;
                })
            )
            .catch((e) => {
                console.error("Error while loading usage data from server.", e);
            })
            .finally(
                action(() => {
                    this.loading = false;
                })
            );
    }
    @action indexNow = () => {
        this.loading = true;

        return Requester.get("/admin/api/index-now/start")
            .then(
                action((response) => {
                    this.data = response;
                })
            )
            .catch((e) => {
                console.error("Error while loading usage data from server.", e);
            })
            .finally(
                action(() => {
                    this.loading = false;
                })
            );
    };

    render() {
        const {urls, responses} = this.data;
        const engines = Object.keys(this.data.responses);
        return (
            <div>
                <h1>{translate("app.index_now_config_headline")}</h1>
                <p>{translate("app.index_now_config_description")}</p>
                <div style={{marginTop: 20, marginBottom: 20}}>
                    <table  className="url-table">
                        <thead className="bg-gray-100">
                        <tr>
                            <th className="px-4 py-2 border">URL</th>
                        </tr>
                        </thead>
                        <tbody>
                        {urls.map((url) => (
                            <tr key={url} className="hover:bg-gray-50">
                                <td className="px-4 py-2 border break-all">{url}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                </div>
                <div style={{marginTop: 20, marginBottom: 20}}>
                    {engines.length > 0 && !this.loading && (
                        <table className="engine-table">
                            <thead className="engine-table-head">
                            <tr>
                                <th>Engine</th>
                                <th>Status</th>
                                <th>Response</th>
                            </tr>
                            </thead>
                            <tbody>
                            {engines.map((engine) => {
                                const res = responses[engine];
                                const status = res?.status;
                                const success = status === 200 || status === 202;
                                return (
                                    <tr key={engine} className="engine-table-row">
                                        <td key={engine}
                                            className={`engine-table-cell ${success ? "success" : "error"}`}>
                                            {engine}
                                        </td>
                                        <td key={engine + status}
                                            className={`engine-table-cell ${success ? "success" : "error"}`}>
                                            {status}
                                        </td>
                                        <td key={engine + "body"}
                                            className={`engine-table-cell ${success ? "success" : "error"}`}>
                                            {res?.body ? JSON.stringify(res.body) : ""}
                                        </td>
                                    </tr>
                                );
                            })}
                            </tbody>
                        </table>
                    )}
                    {this.loading && <Loader/>}
                </div>
            </div>
        );
    }
}

export default withToolbar(IndexNowConfig, function () {
    return {
        items: [
            {
                type: "button",
                label: translate("app.index_now_start"),
                icon: "su-sync",
                disabled: this.loading,
                onClick: () => {
                    this.indexNow().then();
                },
            },
        ],
    };
});
