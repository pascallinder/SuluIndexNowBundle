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
    @observable error = null;

    componentDidMount() {
        this.loadUrls().then();
    }

    @action loadUrls = () => {
        this.loading = true;
        this.error = null;

        return Requester.get("/admin/api/index-now/urls")
            .then(action((response) => {
                    this.data.urls = response.urls;
                })
            )
            .catch((e) => {
                this.error = translate("app.index_now_error_loading");
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
        this.error = null;

        return Requester.post("/admin/api/index-now/start")
            .then(
                action((response) => {
                    this.data = response;
                })
            )
            .catch((e) => {
                this.error = translate("app.index_now_error_submit");
                console.error("Error while loading usage data from server.", e);
            })
            .finally(
                action(() => {
                    this.loading = false;
                })
            );
    };

    render() {
        const {urls} = this.data;
        const responses = this.data.responses || {};
        const batchKeys = Object.keys(responses);
        const engines = batchKeys.flatMap((batchKey) => Object.keys(responses[batchKey] || {}));
        return (
            <div>
                <h1>{translate("app.index_now_config_headline")}</h1>
                <p>{translate("app.index_now_config_description")}</p>
                {this.error && (
                    <div className="notification notification--error">
                        {this.error}
                    </div>
                )}
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
                                <th>Batch</th>
                                <th>Engine</th>
                                <th>Status</th>
                                <th>Response</th>
                            </tr>
                            </thead>
                            <tbody>
                            {batchKeys.map((batchKey) => {
                                const batchResponses = responses[batchKey] || {};
                                return Object.keys(batchResponses).map((engine) => {
                                    const res = batchResponses[engine];
                                    const status = res?.status;
                                    const success = status === 200 || status === 202;
                                    return (
                                        <tr key={`${batchKey}-${engine}`} className="engine-table-row">
                                            <td className={`engine-table-cell ${success ? "success" : "error"}`}>
                                                {batchKey}
                                            </td>
                                            <td className={`engine-table-cell ${success ? "success" : "error"}`}>
                                                {engine}
                                            </td>
                                            <td className={`engine-table-cell ${success ? "success" : "error"}`}>
                                                {status}
                                            </td>
                                            <td className={`engine-table-cell ${success ? "success" : "error"}`}>
                                                {res?.body ? JSON.stringify(res.body) : ""}
                                            </td>
                                        </tr>
                                    );
                                });
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
